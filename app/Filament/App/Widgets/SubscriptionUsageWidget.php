<?php

namespace App\Filament\App\Widgets;

use App\Models\Company;
use Filament\Widgets\Widget;

class SubscriptionUsageWidget extends Widget
{
    protected string $view = 'filament.app.widgets.subscription-usage';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 0; // appears first on dashboard

    // Refresh every 5 minutes so usage stays live
    protected static ?string $pollingInterval = '300s';

    public function getViewData(): array
    {
        /** @var \App\Models\User $user */
        $user    = auth()->user();
        $company = $user?->company;

        if (!$company) {
            return ['usage' => [], 'alerts' => [], 'expiry' => null, 'plan' => null];
        }

        $usage   = $this->buildUsage($company);
        $alerts  = collect($usage)->filter(fn($u) => $u['pct'] >= 80)->values()->toArray();
        $expiry  = $this->buildExpiry($company);

        return [
            'company' => $company,
            'plan'    => $company->subscription,
            'usage'   => $usage,
            'alerts'  => $alerts,
            'expiry'  => $expiry,
        ];
    }

    private function buildUsage(Company $company): array
    {
        $items = [];

        // ── Users ──────────────────────────────────────────────────
        $maxUsers = $company->getEffectiveMaxUsers();
        if ($maxUsers > 0) {
            $usedUsers = $company->users()->count();
            $items[]   = [
                'key'   => 'users',
                'label' => 'Team Members',
                'icon'  => 'heroicon-o-users',
                'used'  => $usedUsers,
                'max'   => $maxUsers,
                'unit'  => 'users',
                'pct'   => min(100, round(($usedUsers / $maxUsers) * 100)),
                'left'  => max(0, $maxUsers - $usedUsers),
            ];
        }

        // ── Projects ───────────────────────────────────────────────
        $maxProjects = $company->getEffectiveMaxProjects();
        if ($maxProjects > 0) {
            $usedProjects = $company->projects()->count();
            $items[]      = [
                'key'   => 'projects',
                'label' => 'Active Projects',
                'icon'  => 'heroicon-o-folder-open',
                'used'  => $usedProjects,
                'max'   => $maxProjects,
                'unit'  => 'projects',
                'pct'   => min(100, round(($usedProjects / $maxProjects) * 100)),
                'left'  => max(0, $maxProjects - $usedProjects),
            ];
        }

        // ── Storage ────────────────────────────────────────────────
        $maxStorageGb = $company->getEffectiveMaxStorageGb();
        if ($maxStorageGb > 0) {
            $usedBytes   = (int) $company->current_storage_bytes;
            $maxBytes    = $maxStorageGb * 1024 ** 3;
            $usedGb      = round($usedBytes / 1024 ** 3, 2);
            $items[]     = [
                'key'   => 'storage',
                'label' => 'Storage',
                'icon'  => 'heroicon-o-circle-stack',
                'used'  => $usedGb,
                'max'   => $maxStorageGb,
                'unit'  => 'GB',
                'pct'   => min(100, $maxBytes > 0 ? round(($usedBytes / $maxBytes) * 100) : 0),
                'left'  => max(0, round($maxStorageGb - $usedGb, 2)),
            ];
        }

        return $items;
    }

    private function buildExpiry(Company $company): ?array
    {
        // Trial expiry
        if ($company->is_trial && $company->trial_ends_at?->isFuture()) {
            $days = (int) now()->diffInDays($company->trial_ends_at);
            return [
                'type'  => 'trial',
                'days'  => $days,
                'date'  => $company->trial_ends_at->format('M j, Y'),
                'urgent'=> $days <= 3,
                'warn'  => $days <= 7,
            ];
        }

        // Subscription expiry
        if ($company->subscription_expires_at && $company->billing_cycle !== 'unlimited') {
            if ($company->subscription_expires_at->isFuture()) {
                $days = (int) now()->diffInDays($company->subscription_expires_at);
                return [
                    'type'  => 'subscription',
                    'days'  => $days,
                    'date'  => $company->subscription_expires_at->format('M j, Y'),
                    'urgent'=> $days <= 3,
                    'warn'  => $days <= 14,
                ];
            }
            // Already expired
            return [
                'type'    => 'expired',
                'days'    => 0,
                'date'    => $company->subscription_expires_at->format('M j, Y'),
                'urgent'  => true,
                'warn'    => true,
            ];
        }

        return null;
    }

    /**
     * Used by the render hook banner to decide whether to show the bar.
     */
    public static function hasAnyAlert(): bool
    {
        $user    = auth()->user();
        $company = $user?->company;
        if (!$company) return false;

        // Check usage thresholds
        $maxUsers = $company->getEffectiveMaxUsers();
        if ($maxUsers && $company->users()->count() / $maxUsers >= 0.8) return true;

        $maxProjects = $company->getEffectiveMaxProjects();
        if ($maxProjects && $company->projects()->count() / $maxProjects >= 0.8) return true;

        $maxStorage = $company->getEffectiveMaxStorageGb();
        if ($maxStorage) {
            $pct = $company->current_storage_bytes / ($maxStorage * 1024 ** 3);
            if ($pct >= 0.8) return true;
        }

        // Check expiry
        if ($company->is_trial && $company->trial_ends_at?->isFuture()) {
            if (now()->diffInDays($company->trial_ends_at) <= 7) return true;
        }
        if ($company->subscription_expires_at && $company->billing_cycle !== 'unlimited') {
            if (!$company->subscription_expires_at->isFuture()) return true;
            if (now()->diffInDays($company->subscription_expires_at) <= 14) return true;
        }

        return false;
    }
}
