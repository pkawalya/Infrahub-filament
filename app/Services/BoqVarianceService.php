<?php

namespace App\Services;

use App\Models\Boq;
use App\Models\BoqItem;
use App\Models\BoqVarianceAlert;
use App\Models\MaterialRequisitionItem;
use App\Models\MaterialIssuance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * BOQ Variance Service
 *
 * Aggregates actual material consumption from:
 *   1. Material Requisitions (quantity_issued per product)
 *   2. BOQ Material Usages (manual field entries)
 *
 * Compares against BOQ item budgets and generates threshold alerts.
 *
 * Threshold levels:
 *   - low:      5% – 10% overrun
 *   - medium:  10% – 20% overrun
 *   - high:    20% – 50% overrun
 *   - critical: 50%+ overrun
 */
class BoqVarianceService
{
    /**
     * Sync all BOQ items for a project against actual consumption.
     */
    public function syncProject(int $projectId, ?int $companyId = null): array
    {
        $boqs = Boq::where('cde_project_id', $projectId)
            ->with(['items.product'])
            ->get();

        $stats = ['items_synced' => 0, 'alerts_created' => 0, 'alerts_resolved' => 0];

        foreach ($boqs as $boq) {
            $result = $this->syncBoq($boq);
            $stats['items_synced'] += $result['items_synced'];
            $stats['alerts_created'] += $result['alerts_created'];
            $stats['alerts_resolved'] += $result['alerts_resolved'];
        }

        return $stats;
    }

    /**
     * Sync a single BOQ against actual consumption.
     */
    public function syncBoq(Boq $boq): array
    {
        $stats = ['items_synced' => 0, 'alerts_created' => 0, 'alerts_resolved' => 0];
        $projectId = $boq->cde_project_id;

        foreach ($boq->items as $item) {
            $this->syncItem($item, $projectId, $boq);
            $stats['items_synced']++;
        }

        // Check for alerts
        $alertResult = $this->evaluateAlerts($boq);
        $stats['alerts_created'] = $alertResult['created'];
        $stats['alerts_resolved'] = $alertResult['resolved'];

        // Recalculate BOQ totals
        $boq->recalculateTotals();

        return $stats;
    }

    /**
     * Sync a single BOQ item: aggregate actual usage from requisitions + manual entries.
     */
    public function syncItem(BoqItem $item, ?int $projectId = null, ?Boq $boq = null): void
    {
        if (!$boq) {
            $boq = $item->boq;
        }
        if (!$projectId) {
            $projectId = $boq->cde_project_id;
        }

        $actualQty = 0;

        // ── Source 1: Issued quantities from Material Requisitions ──
        if ($item->product_id && $projectId) {
            $issuedQty = MaterialRequisitionItem::whereHas('requisition', function ($q) use ($projectId) {
                $q->where('cde_project_id', $projectId)
                    ->whereIn('status', ['approved', 'partially_issued', 'issued']);
            })
                ->where('product_id', $item->product_id)
                ->sum('quantity_issued');

            $actualQty += (float) $issuedQty;

            // ── Source 2: Direct store issuances (not linked to a requisition) ──
            $directIssuedQty = \App\Models\MaterialIssuanceItem::whereHas('issuance', function ($q) use ($projectId) {
                $q->where('cde_project_id', $projectId)
                    ->where('status', 'issued')
                    ->whereNull('material_requisition_id');
            })
                ->where('product_id', $item->product_id)
                ->sum('quantity_issued');
            $actualQty += (float) $directIssuedQty;
        }

        // ── Source 3: Manual BOQ material usage entries ──
        $manualQty = (float) $item->usages()->sum('quantity_used');
        $actualQty += $manualQty;

        // ── Calculate costs ──
        $unitRate = (float) $item->unit_rate;
        $actualCost = $actualQty * $unitRate;
        $budgetedCost = (float) $item->quantity * $unitRate;

        // ── Update item ──
        $item->actual_quantity = $actualQty;
        $item->actual_cost = $actualCost;
        $item->variance_amount = $actualCost - $budgetedCost;
        $item->variance_percent = $budgetedCost > 0
            ? round((($actualCost - $budgetedCost) / $budgetedCost) * 100, 2)
            : 0;
        $item->last_synced_at = now();
        $item->save();
    }

    /**
     * Evaluate all items in a BOQ and create/resolve alerts.
     */
    public function evaluateAlerts(Boq $boq): array
    {
        $created = 0;
        $resolved = 0;

        foreach ($boq->items as $item) {
            $pct = (float) $item->variance_percent;
            $absPct = abs($pct);

            // Only alert if variance ≥ 5%
            if ($absPct < 5) {
                // Auto-resolve any existing alerts for this item if variance has come back
                $resolvedCount = BoqVarianceAlert::where('boq_item_id', $item->id)
                    ->where('is_acknowledged', false)
                    ->update([
                        'is_acknowledged' => true,
                        'acknowledged_at' => now(),
                    ]);
                $resolved += $resolvedCount;
                continue;
            }

            $severity = BoqVarianceAlert::severityFromPercent($pct);
            $alertType = $pct > 0 ? 'overrun' : 'underrun';

            // Check quantity exceedance specifically
            if ($item->actual_quantity > $item->quantity && $item->quantity > 0) {
                $qtyExceedPct = (($item->actual_quantity - $item->quantity) / $item->quantity) * 100;
                if ($qtyExceedPct >= 5) {
                    $alertType = 'quantity_exceeded';
                }
            }

            // Check if an unacknowledged alert already exists for this severity
            $existing = BoqVarianceAlert::where('boq_item_id', $item->id)
                ->where('severity', $severity)
                ->where('is_acknowledged', false)
                ->first();

            if ($existing) {
                // Update the existing alert with the latest numbers
                $existing->update([
                    'budgeted_value' => $item->budgeted_amount,
                    'actual_value' => (float) $item->actual_cost,
                    'variance_percent' => $pct,
                    'message' => $this->buildAlertMessage($item, $pct, $alertType),
                ]);
                continue;
            }

            // Create new alert
            BoqVarianceAlert::create([
                'company_id' => $boq->company_id,
                'boq_id' => $boq->id,
                'boq_item_id' => $item->id,
                'cde_project_id' => $boq->cde_project_id,
                'severity' => $severity,
                'alert_type' => $alertType,
                'title' => $this->buildAlertTitle($item, $severity, $alertType),
                'message' => $this->buildAlertMessage($item, $pct, $alertType),
                'budgeted_value' => $item->budgeted_amount,
                'actual_value' => (float) $item->actual_cost,
                'variance_percent' => $pct,
            ]);
            $created++;

            Log::info("BOQ Variance Alert [{$severity}]: {$item->description} — {$pct}% variance", [
                'boq_id' => $boq->id,
                'boq_item_id' => $item->id,
                'project_id' => $boq->cde_project_id,
            ]);
        }

        return compact('created', 'resolved');
    }

    /**
     * Quick sync: recalculate variance for all BOQ items matching a specific product.
     * Called from the MaterialIssuance observer.
     */
    public function syncByProduct(int $productId, int $projectId): void
    {
        $items = BoqItem::whereHas('boq', function ($q) use ($projectId) {
            $q->where('cde_project_id', $projectId);
        })
            ->where('product_id', $productId)
            ->with('boq')
            ->get();

        foreach ($items as $item) {
            $this->syncItem($item, $projectId);
        }

        // Evaluate alerts for each affected BOQ
        $boqIds = $items->pluck('boq_id')->unique();
        foreach ($boqIds as $boqId) {
            $boq = Boq::with('items')->find($boqId);
            if ($boq) {
                $this->evaluateAlerts($boq);
                $boq->recalculateTotals();
            }
        }
    }

    // ── Private helpers ──

    private function buildAlertTitle(BoqItem $item, string $severity, string $alertType): string
    {
        $typeLabel = match ($alertType) {
            'overrun' => 'Cost Overrun',
            'underrun' => 'Under Budget',
            'quantity_exceeded' => 'Qty Exceeded',
        };
        $sevLabel = strtoupper($severity);

        return "[{$sevLabel}] {$typeLabel}: {$item->description}";
    }

    private function buildAlertMessage(BoqItem $item, float $pct, string $alertType): string
    {
        $budgeted = number_format($item->budgeted_amount, 2);
        $actual = number_format((float) $item->actual_cost, 2);
        $variance = number_format(abs((float) $item->variance_amount), 2);
        $direction = $pct > 0 ? 'over' : 'under';

        $msg = "Item \"{$item->description}\" (#{$item->item_code}) is {$variance} {$direction} budget.";
        $msg .= " Budgeted: {$budgeted}. Actual: {$actual}. Variance: " . abs($pct) . "%.";

        if ($alertType === 'quantity_exceeded') {
            $qtyBudgeted = number_format((float) $item->quantity, 2);
            $qtyActual = number_format((float) $item->actual_quantity, 2);
            $msg .= " Qty budgeted: {$qtyBudgeted} {$item->unit}. Qty consumed: {$qtyActual} {$item->unit}.";
        }

        return $msg;
    }
}
