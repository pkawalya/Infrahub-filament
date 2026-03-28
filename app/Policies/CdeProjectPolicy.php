<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CdeProject;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CdeProjectPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_cde::project');
    }

    public function view(AuthUser $authUser, CdeProject $cdeProject): bool
    {
        return $this->ownedByCompany($authUser, $cdeProject) && $authUser->can('view_cde::project');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_cde::project');
    }

    public function update(AuthUser $authUser, CdeProject $cdeProject): bool
    {
        return $this->ownedByCompany($authUser, $cdeProject) && $authUser->can('update_cde::project');
    }

    public function delete(AuthUser $authUser, CdeProject $cdeProject): bool
    {
        return $this->ownedByCompany($authUser, $cdeProject) && $authUser->can('delete_cde::project');
    }

    public function restore(AuthUser $authUser, CdeProject $cdeProject): bool
    {
        return $this->ownedByCompany($authUser, $cdeProject) && $authUser->can('restore_cde::project');
    }

    public function forceDelete(AuthUser $authUser, CdeProject $cdeProject): bool
    {
        return $this->ownedByCompany($authUser, $cdeProject) && $authUser->can('force_delete_cde::project');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_cde::project');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_cde::project');
    }

    public function replicate(AuthUser $authUser, CdeProject $cdeProject): bool
    {
        return $this->ownedByCompany($authUser, $cdeProject) && $authUser->can('replicate_cde::project');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_cde::project');
    }
}