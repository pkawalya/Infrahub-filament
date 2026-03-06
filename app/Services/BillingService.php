<?php

namespace App\Services;

use App\Models\BillingRecord;
use App\Models\Company;
use App\Models\CdeProject;
use Carbon\Carbon;

class BillingService
{
    /**
     * Calculate the monthly billing for a company.
     *
     * Pricing structure:
     *   Base Platform Fee (from subscription plan)
     * + Per-Project Fee × active projects (beyond included)
     * + Module Fees (per enabled module per project)
     * + Addon Fees (extra users, storage beyond limits)
     * - Discounts
     * + Tax
     * = Total
     */
    public function calculateMonthlyBill(Company $company, ?string $period = null): array
    {
        $period = $period ?? now()->format('Y-m');
        $plan = $company->subscription;

        // ── Base platform fee ────────────────────────────────────
        $baseFee = (float) ($plan?->base_platform_price ?? $plan?->monthly_price ?? 0);

        // ── Active projects this period ──────────────────────────
        $activeProjects = CdeProject::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('billing_status', 'active')
            ->with('moduleAccess')
            ->get();

        $includedProjects = (int) ($plan?->included_projects ?? 0);
        $perProjectPrice = (float) ($plan?->per_project_price ?? 0);
        $modulePrices = $plan?->module_prices ?? [];

        // ── Calculate project fees ───────────────────────────────
        $billableProjectCount = max(0, $activeProjects->count() - $includedProjects);
        $projectFees = $billableProjectCount * $perProjectPrice;

        // ── Calculate module fees per project ────────────────────
        $moduleFees = 0;
        $lineItems = [];

        // Add base fee line
        if ($baseFee > 0) {
            $lineItems[] = [
                'type' => 'platform',
                'description' => ($plan?->name ?? 'Platform') . ' — Base Fee',
                'amount' => $baseFee,
            ];
        }

        // Add per-project lines
        $projectIndex = 0;
        foreach ($activeProjects as $project) {
            $projectIndex++;
            $isIncluded = $projectIndex <= $includedProjects;

            // Per-project base fee
            $projectBaseCost = $isIncluded ? 0 : $perProjectPrice;

            // Module costs for this project
            $projectModuleCost = 0;
            $moduleDetails = [];
            $enabledModules = $project->moduleAccess->where('is_enabled', true);

            foreach ($enabledModules as $moduleAccess) {
                // Priority: project-level override → plan-level → 0
                $price = (float) ($moduleAccess->monthly_price ?: ($modulePrices[$moduleAccess->module_code] ?? 0));
                $projectModuleCost += $price;

                if ($price > 0) {
                    $moduleDetails[] = [
                        'module' => $moduleAccess->module_code,
                        'name' => $moduleAccess->module_name ?? $moduleAccess->module_code,
                        'price' => $price,
                    ];
                }
            }

            $moduleFees += $projectModuleCost;

            $lineItems[] = [
                'type' => 'project',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'project_code' => $project->code,
                'included' => $isIncluded,
                'base_fee' => $projectBaseCost,
                'module_fees' => $projectModuleCost,
                'modules' => $moduleDetails,
                'total' => $projectBaseCost + $projectModuleCost,
            ];
        }

        // ── Addon fees (extra users / storage beyond plan limits) ─
        $addonFees = 0;
        $extraUsers = max(0, $company->users()->count() - ($company->max_users ?? 0));
        $extraUserCost = $extraUsers * 5; // $5 per extra user/month (configurable)
        $addonFees += $extraUserCost;

        if ($extraUserCost > 0) {
            $lineItems[] = [
                'type' => 'addon',
                'description' => "Extra users ({$extraUsers} × \$5/mo)",
                'amount' => $extraUserCost,
            ];
        }

        // ── Totals ───────────────────────────────────────────────
        $subtotal = $baseFee + $projectFees + $moduleFees + $addonFees;
        $discount = 0; // Can be extended for coupon/annual discounts
        $taxRate = (float) ($company->getInvoiceConfig()['default_tax_rate'] ?? 0);
        $taxAmount = ($subtotal - $discount) * ($taxRate / 100);
        $total = $subtotal - $discount + $taxAmount;

        return [
            'company_id' => $company->id,
            'period' => $period,
            'period_start' => Carbon::createFromFormat('Y-m', $period)->startOfMonth(),
            'period_end' => Carbon::createFromFormat('Y-m', $period)->endOfMonth(),
            'base_platform_fee' => round($baseFee, 2),
            'project_fees' => round($projectFees, 2),
            'module_fees' => round($moduleFees, 2),
            'addon_fees' => round($addonFees, 2),
            'discount_amount' => round($discount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round(max(0, $total), 2),
            'active_projects_count' => $activeProjects->count(),
            'active_users_count' => $company->users()->count(),
            'storage_used_gb' => round(($company->current_storage_bytes ?? 0) / (1024 * 1024 * 1024), 2),
            'line_items' => $lineItems,
        ];
    }

    /**
     * Generate (or update draft) billing record for a company and period.
     */
    public function generateBillingRecord(Company $company, ?string $period = null): BillingRecord
    {
        $period = $period ?? now()->format('Y-m');
        $data = $this->calculateMonthlyBill($company, $period);

        return BillingRecord::updateOrCreate(
            ['company_id' => $company->id, 'period' => $period],
            array_merge($data, ['status' => 'draft'])
        );
    }

    /**
     * Finalize a billing record (freeze it, no more changes).
     */
    public function finalizeBillingRecord(BillingRecord $record): BillingRecord
    {
        $record->update([
            'status' => 'finalized',
            'finalized_at' => now(),
        ]);

        return $record->fresh();
    }

    /**
     * Recalculate the monthly rate for a single project.
     */
    public function recalculateProjectRate(CdeProject $project): float
    {
        $plan = $project->company?->subscription;
        $modulePrices = $plan?->module_prices ?? [];
        $perProjectPrice = (float) ($plan?->per_project_price ?? 0);

        $moduleTotal = 0;
        foreach ($project->moduleAccess()->where('is_enabled', true)->get() as $ma) {
            $moduleTotal += (float) ($ma->monthly_price ?: ($modulePrices[$ma->module_code] ?? 0));
        }

        $monthlyRate = $perProjectPrice + $moduleTotal;

        $project->update(['monthly_rate' => $monthlyRate]);

        return $monthlyRate;
    }

    /**
     * Get a cost summary for display (no DB writes).
     */
    public function getCompanyCostSummary(Company $company): array
    {
        $bill = $this->calculateMonthlyBill($company);

        return [
            'plan_name' => $company->subscription?->name ?? 'No Plan',
            'base_fee' => $bill['base_platform_fee'],
            'active_projects' => $bill['active_projects_count'],
            'project_fees' => $bill['project_fees'],
            'module_fees' => $bill['module_fees'],
            'addon_fees' => $bill['addon_fees'],
            'estimated_monthly' => $bill['total_amount'],
            'line_items' => $bill['line_items'],
        ];
    }
}
