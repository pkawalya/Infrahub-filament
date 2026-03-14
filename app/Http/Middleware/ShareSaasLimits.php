<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Shares SaaS plan limit warnings with the application.
 *
 * This middleware does NOT block requests — it injects usage data
 * into views so Filament pages can display contextual warnings
 * (e.g., "You've used 4 of 5 users on your plan").
 *
 * Hard enforcement for user/project creation is handled by
 * the respective Filament resources via beforeCreate hooks.
 */
class ShareSaasLimits
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;

        if (!$company) {
            return $next($request);
        }

        // Build the usage snapshot (cached per-request)
        $saasLimits = $this->buildLimitsSnapshot($company);

        // Share with all views
        view()->share('saasLimits', $saasLimits);

        return $next($request);
    }

    protected function buildLimitsSnapshot(Company $company): array
    {
        $plan = $company->subscription;

        $userCount = $company->users()->count();
        $projectCount = $company->projects()->count();
        $maxUsers = $company->getEffectiveMaxUsers();
        $maxProjects = $company->getEffectiveMaxProjects();
        $maxStorageGb = $company->getEffectiveMaxStorageGb();
        $storageUsedBytes = $company->current_storage_bytes ?? 0;

        return [
            'plan_name' => $plan?->name ?? 'No Plan',
            'is_trial' => $company->isInTrial(),
            'trial_ends_at' => $company->trial_ends_at,
            'subscription_expires_at' => $company->subscription_expires_at,

            // Users
            'users_used' => $userCount,
            'users_max' => $maxUsers,
            'users_remaining' => max(0, $maxUsers - $userCount),
            'users_at_limit' => $maxUsers > 0 && $userCount >= $maxUsers,
            'users_near_limit' => $maxUsers > 0 && $userCount >= ($maxUsers * 0.8),

            // Projects
            'projects_used' => $projectCount,
            'projects_max' => $maxProjects,
            'projects_remaining' => max(0, $maxProjects - $projectCount),
            'projects_at_limit' => $maxProjects > 0 && $projectCount >= $maxProjects,
            'projects_near_limit' => $maxProjects > 0 && $projectCount >= ($maxProjects * 0.8),

            // Storage
            'storage_used_bytes' => $storageUsedBytes,
            'storage_used_formatted' => Company::formatBytes($storageUsedBytes),
            'storage_max_gb' => $maxStorageGb,
            'storage_percent' => $company->getStorageUsagePercent(),
            'storage_at_limit' => $maxStorageGb > 0 && $company->getStorageUsagePercent() >= 100,
            'storage_near_limit' => $maxStorageGb > 0 && $company->getStorageUsagePercent() >= 80,

            // Enabled modules
            'enabled_modules' => $company->getEnabledModules(),
        ];
    }
}
