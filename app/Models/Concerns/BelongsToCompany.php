<?php

namespace App\Models\Concerns;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait for models that belong to a company (tenant scoping).
 * Automatically scopes queries to the current user's company.
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::creating(function ($model) {
            if (empty($model->company_id) && auth()->check()) {
                $model->company_id = auth()->user()->company_id;
            }
        });

        // Global scope: only show records for current company
        static::addGlobalScope('company', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Super admins can see all records across companies
                if ($user->isSuperAdmin()) {
                    return;
                }

                // Users WITH a company see only their company's records
                if ($user->company_id) {
                    $builder->where($builder->getModel()->getTable() . '.company_id', $user->company_id);
                    return;
                }

                // Users WITHOUT a company see NOTHING (return zero results)
                // This prevents orphaned/self-registered users from accessing any data
                $builder->whereRaw('1 = 0');
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
