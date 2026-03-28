<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class AuditLimits extends Command
{
    protected $signature   = 'infrahub:audit-limits {--warn-only : Only show companies near or over limits}';
    protected $description = 'Show subscription usage across all active companies (super-admin tool).';

    public function handle(): int
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int,\App\Models\Company> $companies */
        $companies = Company::with('subscription')
            ->where('is_active', true)
            ->get();

        $rows = [];

        foreach ($companies as $company) {
            $maxUsers    = $company->getEffectiveMaxUsers();
            $maxProjects = $company->getEffectiveMaxProjects();
            $maxStorage  = $company->getEffectiveMaxStorageGb();

            $usedUsers    = $maxUsers    ? $company->getCachedUserCount()    : '—';
            $usedProjects = $maxProjects ? $company->getCachedProjectCount() : '—';
            $usedStorageGb = $maxStorage
                ? round($company->current_storage_bytes / 1024 ** 3, 1)
                : '—';

            $userPct    = $maxUsers    ? round(($usedUsers / $maxUsers) * 100)    : 0;
            $projectPct = $maxProjects ? round(($usedProjects / $maxProjects) * 100) : 0;
            $storagePct = $maxStorage && $company->current_storage_bytes
                ? round(($company->current_storage_bytes / ($maxStorage * 1024 ** 3)) * 100)
                : 0;

            $expiry = '—';
            if ($company->is_trial && $company->trial_ends_at?->isFuture()) {
                $days   = (int) now()->diffInDays($company->trial_ends_at);
                $expiry = "TRIAL {$days}d";
            } elseif ($company->subscription_expires_at && $company->billing_cycle !== 'unlimited') {
                if (!$company->subscription_expires_at->isFuture()) {
                    $expiry = '⛔ EXPIRED';
                } else {
                    $days   = (int) now()->diffInDays($company->subscription_expires_at);
                    $expiry = "{$days}d";
                }
            } elseif ($company->billing_cycle === 'unlimited') {
                $expiry = '∞';
            }

            $hasWarning = $userPct >= 80 || $projectPct >= 80 || $storagePct >= 80
                || str_contains($expiry, 'EXPIRED')
                || (is_numeric(rtrim($expiry, 'd')) && (int) rtrim($expiry, 'd') <= 14);

            if ($this->option('warn-only') && !$hasWarning) continue;

            $flag = $hasWarning ? '⚠' : ' ';

            $rows[] = [
                $flag . ' ' . $company->name,
                $company->subscription?->name ?? $company->billing_cycle ?? '—',
                $maxUsers    ? "{$usedUsers}/{$maxUsers} ({$userPct}%)"    : '∞',
                $maxProjects ? "{$usedProjects}/{$maxProjects} ({$projectPct}%)" : '∞',
                $maxStorage  ? "{$usedStorageGb}/{$maxStorage}GB ({$storagePct}%)" : '∞',
                $expiry,
            ];
        }

        $this->table(
            ['Company', 'Plan', 'Users', 'Projects', 'Storage', 'Expiry'],
            $rows
        );

        $warnings = collect($rows)->filter(fn($r) => str_starts_with(trim($r[0]), '⚠'))->count();
        $this->line("\n{$warnings} company/companies with warnings out of {$companies->count()} total.");

        return self::SUCCESS;
    }
}
