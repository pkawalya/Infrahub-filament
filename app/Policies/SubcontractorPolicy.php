<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Subcontractor;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SubcontractorPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_subcontractor');
    }

    public function view(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $this->ownedByCompany($authUser, $subcontractor) && $authUser->can('view_subcontractor');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_subcontractor');
    }

    public function update(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $this->ownedByCompany($authUser, $subcontractor) && $authUser->can('update_subcontractor');
    }

    public function delete(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $this->ownedByCompany($authUser, $subcontractor) && $authUser->can('delete_subcontractor');
    }

    public function restore(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $this->ownedByCompany($authUser, $subcontractor) && $authUser->can('restore_subcontractor');
    }

    public function forceDelete(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $this->ownedByCompany($authUser, $subcontractor) && $authUser->can('force_delete_subcontractor');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_subcontractor');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_subcontractor');
    }

    public function replicate(AuthUser $authUser, Subcontractor $subcontractor): bool
    {
        return $this->ownedByCompany($authUser, $subcontractor) && $authUser->can('replicate_subcontractor');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_subcontractor');
    }
}