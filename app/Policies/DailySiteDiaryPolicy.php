<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DailySiteDiary;
use Illuminate\Auth\Access\HandlesAuthorization;

class DailySiteDiaryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_daily::site::diary');
    }

    public function view(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $authUser->can('view_daily::site::diary');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_daily::site::diary');
    }

    public function update(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $authUser->can('update_daily::site::diary');
    }

    public function delete(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $authUser->can('delete_daily::site::diary');
    }

    public function restore(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $authUser->can('restore_daily::site::diary');
    }

    public function forceDelete(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $authUser->can('force_delete_daily::site::diary');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_daily::site::diary');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_daily::site::diary');
    }

    public function replicate(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $authUser->can('replicate_daily::site::diary');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_daily::site::diary');
    }

}