<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Asset;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_asset');
    }

    public function view(AuthUser $authUser, Asset $asset): bool
    {
        return $authUser->can('view_asset');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_asset');
    }

    public function update(AuthUser $authUser, Asset $asset): bool
    {
        return $authUser->can('update_asset');
    }

    public function delete(AuthUser $authUser, Asset $asset): bool
    {
        return $authUser->can('delete_asset');
    }

    public function restore(AuthUser $authUser, Asset $asset): bool
    {
        return $authUser->can('restore_asset');
    }

    public function forceDelete(AuthUser $authUser, Asset $asset): bool
    {
        return $authUser->can('force_delete_asset');
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
        return $authUser->can('replicate_asset');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_asset');
    }

}