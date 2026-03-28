<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Asset;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AssetPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_asset');
    }

    public function view(AuthUser $authUser, Asset $asset): bool
    {
        return $this->ownedByCompany($authUser, $asset) && $authUser->can('view_asset');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_asset');
    }

    public function update(AuthUser $authUser, Asset $asset): bool
    {
        return $this->ownedByCompany($authUser, $asset) && $authUser->can('update_asset');
    }

    public function delete(AuthUser $authUser, Asset $asset): bool
    {
        return $this->ownedByCompany($authUser, $asset) && $authUser->can('delete_asset');
    }

    public function restore(AuthUser $authUser, Asset $asset): bool
    {
        return $this->ownedByCompany($authUser, $asset) && $authUser->can('restore_asset');
    }

    public function forceDelete(AuthUser $authUser, Asset $asset): bool
    {
        return $this->ownedByCompany($authUser, $asset) && $authUser->can('force_delete_asset');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_asset');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_asset');
    }

    public function replicate(AuthUser $authUser, Asset $asset): bool
    {
        return $this->ownedByCompany($authUser, $asset) && $authUser->can('replicate_asset');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_asset');
    }
}