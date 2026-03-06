<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Invoice;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessFinancialAlerts extends Command
{
    protected $signature = 'financial:process-alerts';
    protected $description = 'Flag overdue invoices and send subscription expiry warnings';

    public function handle(): int
    {
        $this->info('Processing financial alerts...');

        $overdue = $this->flagOverdueInvoices();
        $expiring = $this->checkExpiringSubscriptions();

        $this->info("Done. Overdue invoices flagged: {$overdue}. Expiring subscriptions warned: {$expiring}.");

        return self::SUCCESS;
    }

    /**
     * Auto-flag invoices as overdue if due_date is past and status is sent/partially_paid.
     */
    protected function flagOverdueInvoices(): int
    {
        $count = Invoice::whereIn('status', ['sent', 'partially_paid'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->startOfDay())
            ->update(['status' => 'overdue']);

        if ($count > 0) {
            Log::info("FinancialAlerts: Flagged {$count} invoices as overdue.");
            $this->line("  ⚠ {$count} invoices marked as overdue.");

            // Notify company admins about newly overdue invoices
            $overdueInvoices = Invoice::where('status', 'overdue')
                ->where('due_date', '>=', now()->subDays(1)->startOfDay())
                ->where('due_date', '<', now()->startOfDay())
                ->with('company')
                ->get();

            $emailService = app(EmailService::class);

            foreach ($overdueInvoices->groupBy('company_id') as $companyId => $invoices) {
                $company = $invoices->first()->company;
                if (!$company)
                    continue;

                // Try to send an overdue notification email to the company admin
                $admins = $company->users()->whereHas('roles', fn($q) => $q->where('name', 'like', '%admin%'))->get();

                foreach ($admins as $admin) {
                    $emailService->send('invoice-overdue', $admin, [
                        'overdue_count' => $invoices->count(),
                        'total_overdue_amount' => $invoices->sum('balance_due'),
                        'invoice_numbers' => $invoices->pluck('invoice_number')->join(', '),
                    ], $companyId);
                }
            }
        }

        return $count;
    }

    /**
     * Check for subscriptions expiring within 14 days and send warnings.
     */
    protected function checkExpiringSubscriptions(): int
    {
        $warned = 0;

        if (!DB::getSchemaBuilder()->hasTable('company_subscriptions')) {
            $this->line('  ℹ company_subscriptions table not found — skipping.');
            return 0;
        }

        $expiringCompanies = DB::table('company_subscriptions')
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), now()->addDays(14)])
            ->get();

        if ($expiringCompanies->isEmpty()) {
            $this->line('  ✓ No expiring subscriptions.');
            return 0;
        }

        $emailService = app(EmailService::class);

        foreach ($expiringCompanies as $sub) {
            $company = Company::find($sub->company_id);
            if (!$company)
                continue;

            $daysLeft = now()->diffInDays($sub->ends_at, false);

            // Send to company admins
            $admins = $company->users()->whereHas('roles', fn($q) => $q->where('name', 'like', '%admin%'))->get();

            foreach ($admins as $admin) {
                $sent = $emailService->send('subscription-expiring', $admin, [
                    'days_remaining' => $daysLeft,
                    'expiry_date' => \Carbon\Carbon::parse($sub->ends_at)->format('M d, Y'),
                    'plan_name' => $sub->plan_name ?? 'Current Plan',
                ], $company->id);

                if ($sent)
                    $warned++;
            }

            $this->line("  📧 Warned company '{$company->name}' — {$daysLeft} days left.");
        }

        return $warned;
    }
}
