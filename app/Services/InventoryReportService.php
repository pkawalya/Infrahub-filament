<?php

namespace App\Services;

use App\Models\InventoryAuditLog;
use App\Models\MaterialIssuance;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockLevel;
use App\Support\CurrencyHelper;
use Carbon\Carbon;

/**
 * Centralised data service for inventory intelligence reports.
 * Used by both InventoryPage and ReportingPage.
 */
class InventoryReportService
{
    /**
     * Stock Valuation: products × unit cost × qty on hand
     */
    public function stockValuation(int $companyId): array
    {
        $levels = StockLevel::whereHas('product', fn($q) => $q->where('company_id', $companyId))
            ->with(['product.category', 'warehouse'])
            ->get();

        $byProduct = $levels->groupBy('product_id')->map(function ($group) {
            $product    = $group->first()->product;
            $totalUnits = $group->sum('quantity_on_hand');
            $unitCost   = (float) ($product->cost_price ?? 0);
            return [
                'product_name'  => $product->name ?? '—',
                'sku'           => $product->sku ?? '—',
                'category'      => $product->category?->name ?? 'Uncategorised',
                'unit'          => $product->unit_of_measure ?? 'each',
                'unit_cost'     => $unitCost,
                'total_units'   => $totalUnits,
                'total_value'   => round($totalUnits * $unitCost, 2),
                'reorder_level' => (int) ($product->reorder_level ?? 0),
                'max_level'     => (int) ($product->max_order_level ?? 0),
                'is_low'        => ($product->reorder_level ?? 0) > 0 && $totalUnits <= ($product->reorder_level ?? 0),
                'is_over'       => ($product->max_order_level ?? 0) > 0 && $totalUnits > ($product->max_order_level ?? 0),
                'warehouses'    => $group->map(fn($sl) => [
                    'name'     => $sl->warehouse?->name ?? '—',
                    'on_hand'  => $sl->quantity_on_hand,
                    'reserved' => $sl->quantity_reserved ?? 0,
                ])->values()->toArray(),
            ];
        })->sortByDesc('total_value')->values();

        $totalValue     = $byProduct->sum('total_value');
        $totalUnits     = $byProduct->sum('total_units');
        $lowStockCount  = $byProduct->where('is_low', true)->count();
        $overStockCount = $byProduct->where('is_over', true)->count();

        $byWarehouse = $levels->groupBy('warehouse_id')->map(function ($group) {
            $wh = $group->first()->warehouse;
            return [
                'warehouse'   => $wh?->name ?? '—',
                'total_units' => $group->sum('quantity_on_hand'),
                'total_value' => $group->reduce(fn($c, $sl) => $c + ($sl->quantity_on_hand * (float) ($sl->product?->cost_price ?? 0)), 0.0),
                'products'    => $group->count(),
            ];
        })->sortByDesc('total_value')->values();

        return [
            'summary'      => [
                'total_value'    => $totalValue,
                'total_units'    => $totalUnits,
                'total_products' => $byProduct->count(),
                'low_stock'      => $lowStockCount,
                'over_stock'     => $overStockCount,
            ],
            'by_product'   => $byProduct->toArray(),
            'by_warehouse' => $byWarehouse->toArray(),
        ];
    }

    /**
     * Slow-Moving: products with no ProductTracking event in N days
     */
    public function slowMoving(int $companyId, int $days = 90): array
    {
        $threshold = now()->subDays($days);

        $products = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->with(['stockLevels', 'tracking' => fn($q) => $q->latest()->limit(1)])
            ->get();

        $slowMoving = $products->filter(function ($p) use ($threshold) {
            $last = $p->tracking->first();
            return !$last || $last->created_at->lt($threshold);
        })->map(function ($p) {
            $last  = $p->tracking->first();
            $stock = $p->stockLevels->sum('quantity_on_hand');
            return [
                'name'          => $p->name,
                'sku'           => $p->sku ?? '—',
                'stock'         => $stock,
                'cost_price'    => (float) $p->cost_price,
                'stock_value'   => $stock * (float) $p->cost_price,
                'last_movement' => $last?->created_at?->format('M d, Y') ?? 'No movements',
                'days_static'   => $last ? $last->created_at->diffInDays(now()) : null,
            ];
        })->sortByDesc('stock_value')->values();

        return [
            'days_threshold'    => $days,
            'slow_moving_count' => $slowMoving->count(),
            'locked_value'      => $slowMoving->sum('stock_value'),
            'items'             => $slowMoving->toArray(),
        ];
    }

    /**
     * Expiry: products with expiry_tracking_enabled and expiry_date set
     */
    public function expiryTracking(int $companyId): array
    {
        $products = Product::where('company_id', $companyId)
            ->where('expiry_tracking_enabled', true)
            ->whereNotNull('expiry_date')
            ->with('stockLevels')
            ->orderBy('expiry_date')
            ->get();

        $expired      = $products->filter(fn($p) => Carbon::parse($p->expiry_date)->isPast());
        $expiringSoon = $products->filter(fn($p) => Carbon::parse($p->expiry_date)->isFuture() && Carbon::parse($p->expiry_date)->lte(now()->addDays(30)));
        $safe         = $products->filter(fn($p) => Carbon::parse($p->expiry_date)->gt(now()->addDays(30)));

        $map = fn($p) => [
            'name'        => $p->name,
            'sku'         => $p->sku ?? '—',
            'expiry_date' => Carbon::parse($p->expiry_date)->format('M d, Y'),
            'days_left'   => Carbon::parse($p->expiry_date)->diffInDays(now(), false),
            'stock'       => $p->stockLevels->sum('quantity_on_hand'),
            'stock_value' => $p->stockLevels->sum('quantity_on_hand') * (float) $p->cost_price,
        ];

        return [
            'expired'       => $expired->map($map)->values()->toArray(),
            'expiring_soon' => $expiringSoon->map($map)->values()->toArray(),
            'safe'          => $safe->map($map)->values()->toArray(),
            'summary' => [
                'expired_count'       => $expired->count(),
                'expiring_soon_count' => $expiringSoon->count(),
                'safe_count'          => $safe->count(),
                'expired_value'       => $expired->reduce(fn($c, $p) => $c + $p->stockLevels->sum('quantity_on_hand') * (float) $p->cost_price, 0.0),
                'expiring_value'      => $expiringSoon->reduce(fn($c, $p) => $c + $p->stockLevels->sum('quantity_on_hand') * (float) $p->cost_price, 0.0),
            ],
        ];
    }

    /**
     * Audit Trail: inventory_audit_logs for URA compliance
     */
    public function auditTrail(int $companyId, Carbon $from, Carbon $to, int $limit = 200): array
    {
        $logs = InventoryAuditLog::where('company_id', $companyId)
            ->whereBetween('created_at', [$from, $to])
            ->with(['product', 'warehouse', 'performer'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $byEvent    = collect($logs->groupBy('event_type')->map(fn($g) => $g->count())->toArray())->sortDesc();
        $totalValue = $logs->sum('total_value');

        return [
            'events'        => $logs->map(fn($l) => [
                'date'         => $l->created_at->format('M d, Y H:i'),
                'event_type'   => InventoryAuditLog::$eventTypes[$l->event_type] ?? $l->event_type,
                'reference'    => $l->reference_number ?? '—',
                'product'      => $l->product?->name ?? '—',
                'warehouse'    => $l->warehouse?->name ?? '—',
                'qty_before'   => $l->quantity_before,
                'qty_change'   => $l->quantity_change,
                'qty_after'    => $l->quantity_after,
                'unit_cost'    => (float) $l->unit_cost,
                'total_value'  => (float) $l->total_value,
                'description'  => $l->description,
                'performed_by' => $l->performer?->name ?? '—',
                'ip_address'   => $l->ip_address,
            ])->toArray(),
            'by_event_type' => $byEvent->toArray(),
            'total_value'   => $totalValue,
            'total_events'  => $logs->count(),
        ];
    }

    /**
     * Procurement: PO summary + quarterly breakdown + issuance summary
     */
    public function procurement(int $companyId, int $projectId, Carbon $from, Carbon $to): array
    {
        $pos = PurchaseOrder::where('cde_project_id', $projectId)
            ->whereBetween('created_at', [$from, $to])
            ->with(['supplier', 'items', 'creator'])
            ->get();

        $totalPOValue = $pos->sum('total_amount');
        $byStatus     = collect($pos->groupBy('status')->map(fn($g) => $g->count())->toArray());
        $bySupplier   = $pos->groupBy('supplier_id')->map(fn($g) => [
            'name'  => $g->first()->supplier?->name ?? 'Unknown',
            'count' => $g->count(),
            'value' => $g->sum('total_amount'),
        ])->sortByDesc('value')->values();

        $quarterly = [];
        for ($i = 3; $i >= 0; $i--) {
            $q    = now()->subQuarters($i);
            $qs   = 'Q' . $q->quarter . '-' . $q->year;
            $qPos = $pos->filter(fn($po) => $po->created_at->quarter === $q->quarter && $po->created_at->year === $q->year);
            $quarterly[] = ['quarter' => $qs, 'count' => $qPos->count(), 'value' => $qPos->sum('total_amount')];
        }

        $issuances = MaterialIssuance::where('company_id', $companyId)
            ->where('cde_project_id', $projectId)
            ->whereBetween('created_at', [$from, $to])
            ->with(['issuedTo'])
            ->get();

        $issuedByPurpose = collect($issuances->groupBy('purpose')->map(fn($g) => $g->count())->toArray());
        $issuedByPerson  = collect($issuances->groupBy(fn($i) => $i->issuedTo?->name ?? $i->issued_to_name ?? 'External')
            ->map(fn($g) => $g->count())->toArray())->sortDesc()->take(10);

        return [
            'po_summary'       => ['total_pos' => $pos->count(), 'total_value' => $totalPOValue, 'by_status' => $byStatus->toArray()],
            'by_supplier'      => $bySupplier,
            'quarterly'        => $quarterly,
            'pos'              => $pos->map(fn($po) => [
                'po_number'    => $po->po_number,
                'supplier'     => $po->supplier?->name ?? '—',
                'status'       => $po->status,
                'order_date'   => $po->order_date?->format('M d, Y'),
                'expected'     => $po->expected_date?->format('M d, Y'),
                'total'        => (float) $po->total_amount,
                'items'        => $po->items->count(),
                'created_by'   => $po->creator?->name ?? '—',
                'quarter'      => $po->quarter ?? null,
                'is_quarterly' => (bool) $po->is_quarterly,
            ])->toArray(),
            'issuance_summary' => [
                'total_issuances' => $issuances->count(),
                'by_purpose'      => $issuedByPurpose->toArray(),
                'by_person'       => $issuedByPerson->toArray(),
            ],
        ];
    }
}
