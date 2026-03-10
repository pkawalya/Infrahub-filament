<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces tenant isolation on all API requests.
 * Ensures users can only access data belonging to their company.
 * Adds company_id context to all queries via a global scope.
 */
class EnforceTenantIsolation
{
    /**
     * Models that must be tenant-scoped.
     */
    protected array $tenantModels = [
        \App\Models\CdeProject::class,
        \App\Models\Task::class,
        \App\Models\Invoice::class,
        \App\Models\Contract::class,
        \App\Models\ChangeOrder::class,
        \App\Models\Drawing::class,
        \App\Models\PaymentCertificate::class,
        \App\Models\WorkOrder::class,
        \App\Models\Asset::class,
        \App\Models\EquipmentAllocation::class,
        \App\Models\EquipmentFuelLog::class,
        \App\Models\SafetyIncident::class,
        \App\Models\DailySiteDiary::class,
        \App\Models\CrewAttendance::class,
        \App\Models\Subcontractor::class,
        \App\Models\Tender::class,
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Super admins can access all data
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $companyId = $user->company_id;

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'No company associated with your account.',
            ], 403);
        }

        // Apply global scope to tenant models
        foreach ($this->tenantModels as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::addGlobalScope('tenant', function ($query) use ($companyId) {
                    if ($query->getModel()->getTable() !== 'users') {
                        $query->where($query->getModel()->getTable() . '.company_id', $companyId);
                    }
                });
            }
        }

        return $next($request);
    }
}
