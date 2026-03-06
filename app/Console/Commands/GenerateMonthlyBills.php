<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\BillingService;
use Illuminate\Console\Command;

class GenerateMonthlyBills extends Command
{
    protected $signature = 'billing:generate
                            {--period= : Billing period in YYYY-MM format (defaults to current month)}
                            {--company= : Generate for a specific company ID only}
                            {--finalize : Auto-finalize the bills after generation}';

    protected $description = 'Generate monthly billing records for all active companies';

    public function handle(BillingService $billingService): int
    {
        $period = $this->option('period') ?? now()->format('Y-m');
        $companyId = $this->option('company');
        $finalize = $this->option('finalize');

        $this->info("Generating bills for period: {$period}");

        $query = Company::where('is_active', true);
        if ($companyId) {
            $query->where('id', $companyId);
        }

        $companies = $query->get();
        $this->info("Processing {$companies->count()} companies...");

        $bar = $this->output->createProgressBar($companies->count());
        $generated = 0;

        foreach ($companies as $company) {
            $record = $billingService->generateBillingRecord($company, $period);

            if ($finalize && $record->status === 'draft') {
                $billingService->finalizeBillingRecord($record);
            }

            $generated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Generated {$generated} billing records.");

        if ($this->getOutput()->isVerbose()) {
            $this->table(
                ['Company', 'Projects', 'Base Fee', 'Project Fees', 'Module Fees', 'Total'],
                Company::where('is_active', true)
                    ->when($companyId, fn($q) => $q->where('id', $companyId))
                    ->get()
                    ->map(function ($company) use ($period) {
                        $record = $company->billingRecords()->where('period', $period)->first();
                        return $record ? [
                            $company->name,
                            $record->active_projects_count,
                            number_format($record->base_platform_fee, 2),
                            number_format($record->project_fees, 2),
                            number_format($record->module_fees, 2),
                            number_format($record->total_amount, 2),
                        ] : [$company->name, '-', '-', '-', '-', 'No record'];
                    })
            );
        }

        return Command::SUCCESS;
    }
}
