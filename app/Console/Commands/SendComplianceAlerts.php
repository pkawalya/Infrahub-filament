<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Subcontractor;
use App\Models\Tender;
use App\Models\WorkerCertification;
use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendComplianceAlerts extends Command
{
    protected $signature = 'alerts:compliance {--dry-run : Show what would be sent without sending}';
    protected $description = 'Send daily notifications for expiring certifications, insurance, overdue tenders, and equipment maintenance';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $today = now();
        $warningDate = $today->copy()->addDays(30);
        $alertCount = 0;

        $this->info('🔍 Scanning compliance alerts...');

        // ── Subcontractor Insurance/License Expiry ───────────────────
        $expiringSubs = Subcontractor::query()
            ->withoutGlobalScopes()
            ->where('status', 'active')
            ->where(function ($q) use ($today, $warningDate) {
                $q->whereBetween('insurance_expiry', [$today, $warningDate])
                    ->orWhereBetween('license_expiry', [$today, $warningDate]);
            })
            ->with('company')
            ->get();

        foreach ($expiringSubs as $sub) {
            $issues = [];
            if ($sub->insurance_expiry && $sub->insurance_expiry->between($today, $warningDate)) {
                $days = $today->diffInDays($sub->insurance_expiry);
                $issues[] = "Insurance expires in {$days} days ({$sub->insurance_expiry->format('M d')})";
            }
            if ($sub->license_expiry && $sub->license_expiry->between($today, $warningDate)) {
                $days = $today->diffInDays($sub->license_expiry);
                $issues[] = "License expires in {$days} days ({$sub->license_expiry->format('M d')})";
            }

            $message = "Subcontractor '{$sub->name}': " . implode(', ', $issues);
            $this->warn("  ⚠ {$message}");

            if (!$dryRun) {
                $this->sendDatabaseNotification(
                    $sub->company,
                    'Subcontractor Compliance Alert',
                    $message,
                    'heroicon-o-shield-exclamation',
                    'warning'
                );
            }
            $alertCount++;
        }

        // ── Worker Certification Expiry ──────────────────────────────
        $expiringCerts = WorkerCertification::query()
            ->withoutGlobalScopes()
            ->whereBetween('expiry_date', [$today, $warningDate])
            ->with(['worker', 'company'])
            ->get();

        foreach ($expiringCerts as $cert) {
            $days = $today->diffInDays($cert->expiry_date);
            $message = "{$cert->worker?->name}'s '{$cert->certification_name}' expires in {$days} days";
            $this->warn("  ⚠ {$message}");

            if (!$dryRun) {
                $this->sendDatabaseNotification(
                    $cert->company,
                    'Certification Expiry Warning',
                    $message,
                    'heroicon-o-identification',
                    'warning'
                );
            }
            $alertCount++;
        }

        // ── Tender Deadline Approaching (7 days or less) ─────────────
        $urgentTenders = Tender::query()
            ->withoutGlobalScopes()
            ->whereIn('status', ['identified', 'preparing'])
            ->whereNotNull('submission_deadline')
            ->whereBetween('submission_deadline', [$today, $today->copy()->addDays(7)])
            ->with('company')
            ->get();

        foreach ($urgentTenders as $tender) {
            $days = $today->diffInDays($tender->submission_deadline);
            $message = "Tender '{$tender->title}' deadline in {$days} day(s) — {$tender->submission_deadline->format('M d')}";
            $this->warn("  📋 {$message}");

            if (!$dryRun) {
                $this->sendDatabaseNotification(
                    $tender->company,
                    'Tender Deadline Approaching',
                    $message,
                    'heroicon-o-clock',
                    'danger'
                );
            }
            $alertCount++;
        }

        // ── Equipment Maintenance Overdue ────────────────────────────
        $overdueAssets = Asset::query()
            ->withoutGlobalScopes()
            ->whereNotNull('next_service_date')
            ->where('next_service_date', '<', $today)
            ->whereIn('status', ['available', 'assigned'])
            ->with('company')
            ->get();

        foreach ($overdueAssets as $asset) {
            $days = $asset->next_service_date->diffInDays($today);
            $message = "Equipment '{$asset->display_name}' service overdue by {$days} days";
            $this->warn("  🔧 {$message}");

            if (!$dryRun) {
                $this->sendDatabaseNotification(
                    $asset->company,
                    'Equipment Maintenance Overdue',
                    $message,
                    'heroicon-o-wrench-screwdriver',
                    'danger'
                );
            }
            $alertCount++;
        }

        $mode = $dryRun ? '(dry-run)' : '';
        $this->info("✅ Compliance scan complete. {$alertCount} alerts found {$mode}.");

        return self::SUCCESS;
    }

    /**
     * Send a database notification to all users in a company.
     */
    private function sendDatabaseNotification(
        mixed $company,
        string $title,
        string $body,
        string $icon,
        string $color
    ): void {
        if (!$company || !($company instanceof Company))
            return;

        $users = $company->users()->limit(10)->get();

        foreach ($users as $user) {
            \Filament\Notifications\Notification::make()
                ->title($title)
                ->body($body)
                ->icon($icon)
                ->color($color)
                ->sendToDatabase($user);
        }
    }
}

