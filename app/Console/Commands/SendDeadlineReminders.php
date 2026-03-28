<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Task;
use App\Models\WorkOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskDueSoonNotification;
use App\Notifications\WorkOrderDueSoonNotification;
use App\Notifications\SubscriptionLimitWarningNotification;

class SendDeadlineReminders extends Command
{
    protected $signature   = 'infrahub:deadline-reminders';
    protected $description = 'Send upcoming task/work-order deadline reminders and subscription limit warnings.';

    public function handle(): int
    {
        $this->info('Sending deadline reminders...');
        $sent = 0;

        // ── Task reminders ────────────────────────────────────────────
        $dueSoon = Task::with('assignee')
            ->whereNotNull('assigned_to')
            ->whereNotIn('status', ['done', 'cancelled'])
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays(2)->endOfDay()])
            ->get();

        foreach ($dueSoon as $task) {
            if (!$task->assignee) continue;
            try {
                $task->assignee->notify(new TaskDueSoonNotification($task));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("Deadline reminder failed for task #{$task->id}: " . $e->getMessage());
            }
        }
        $this->line("  Tasks: {$dueSoon->count()} reminders queued.");

        // ── Work Order reminders ──────────────────────────────────────
        $wosDue = WorkOrder::with('assignee')
            ->whereNotNull('assigned_to')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays(2)->endOfDay()])
            ->get();

        foreach ($wosDue as $wo) {
            if (!$wo->assignee) continue;
            try {
                $wo->assignee->notify(new WorkOrderDueSoonNotification($wo));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("Deadline reminder failed for WO #{$wo->id}: " . $e->getMessage());
            }
        }
        $this->line("  Work Orders: {$wosDue->count()} reminders queued.");

        // ── Subscription limit warnings ───────────────────────────────
        $companies = Company::with(['users' => fn($q) => $q->where('is_company_admin', true)])
            ->where('is_active', true)
            ->get();

        foreach ($companies as $company) {
            try {
                $this->checkAndWarnLimits($company);
            } catch (\Throwable $e) {
                Log::warning("Subscription limit check failed for company #{$company->id}: " . $e->getMessage());
            }
        }

        $this->info("Done. Total notifications sent: {$sent}");
        return self::SUCCESS;
    }

    private function checkAndWarnLimits(Company $company): void
    {
        $admin = $company->users()
            ->where('is_active', true)
            ->where(fn($q) => $q->where('is_company_admin', true)->orWhere('is_super_admin', true))
            ->first();

        if (!$admin) return;

        $warnings = [];

        // Users limit
        $maxUsers = $company->getEffectiveMaxUsers();
        if ($maxUsers > 0) {
            $usedPct = round(($company->getCachedUserCount() / $maxUsers) * 100);
            if ($usedPct >= 90) {
                $warnings[] = "Team members: {$usedPct}% used ({$company->getCachedUserCount()}/{$maxUsers})";
            }
        }

        // Projects limit
        $maxProjects = $company->getEffectiveMaxProjects();
        if ($maxProjects > 0) {
            $usedPct = round(($company->getCachedProjectCount() / $maxProjects) * 100);
            if ($usedPct >= 90) {
                $warnings[] = "Projects: {$usedPct}% used ({$company->getCachedProjectCount()}/{$maxProjects})";
            }
        }

        // Storage limit
        $maxStorage = $company->getEffectiveMaxStorageGb();
        if ($maxStorage > 0 && $company->current_storage_bytes > 0) {
            $usedPct = round(($company->current_storage_bytes / ($maxStorage * 1024 ** 3)) * 100);
            if ($usedPct >= 90) {
                $usedGb = round($company->current_storage_bytes / 1024 ** 3, 1);
                $warnings[] = "Storage: {$usedPct}% used ({$usedGb}/{$maxStorage} GB)";
            }
        }

        // Trial expiry (≤7 days)
        if ($company->is_trial && $company->trial_ends_at?->isFuture()) {
            $days = (int) now()->diffInDays($company->trial_ends_at);
            if ($days <= 7) {
                $warnings[] = "Trial expires in {$days} " . \Illuminate\Support\Str::plural('day', $days);
            }
        }

        // Subscription expiry (≤14 days)
        if ($company->subscription_expires_at && $company->billing_cycle !== 'unlimited') {
            if (!$company->subscription_expires_at->isFuture()) {
                $warnings[] = "Subscription expired on {$company->subscription_expires_at->format('M j, Y')}";
            } elseif (now()->diffInDays($company->subscription_expires_at) <= 14) {
                $days = (int) now()->diffInDays($company->subscription_expires_at);
                $warnings[] = "Subscription renews in {$days} " . \Illuminate\Support\Str::plural('day', $days);
            }
        }

        if (empty($warnings)) return;

        // Only send once per day (deduplicate via cache)
        $cacheKey = "limit_warning_sent:{$company->id}:" . now()->toDateString();
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) return;

        $admin->notify(new SubscriptionLimitWarningNotification($company, $warnings));
        \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->endOfDay());

        $this->line("  Limit warning sent to {$admin->email} for {$company->name}");
    }
}
