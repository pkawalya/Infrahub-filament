<?php

namespace App\Policies\Concerns;

use Illuminate\Foundation\Auth\User as AuthUser;

/**
 * Mixin for all tenant-scoped Policies.
 *
 * Adds company_id ownership verification to record-level policy methods.
 * Super-admins bypass the company check but still need the permission.
 *
 * Usage in a policy:
 *   use EnforcesCompanyOwnership;
 *   public function view(AuthUser $user, MyModel $record): bool {
 *       return $this->ownedByCompany($user, $record) && $user->can('view_my_model');
 *   }
 */
trait EnforcesCompanyOwnership
{
    /**
     * Returns true if:
     *   (a) the user is a super-admin (can see everything), OR
     *   (b) the record's company_id matches the user's company_id.
     *
     * @param  AuthUser            $user
     * @param  object              $record  Any model with a company_id property
     */
    protected function ownedByCompany(AuthUser $user, object $record): bool
    {
        // Super-admins bypass tenant isolation at the policy level
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return isset($record->company_id)
            && (int) $record->company_id === (int) $user->company_id;
    }
}
