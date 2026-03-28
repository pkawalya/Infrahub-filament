<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DailySiteDiary;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DailySiteDiaryPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_daily::site::diary');
    }

    public function view(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $this->ownedByCompany($authUser, $dailySiteDiary) && $authUser->can('view_daily::site::diary');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_daily::site::diary');
    }

    public function update(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $this->ownedByCompany($authUser, $dailySiteDiary) && $authUser->can('update_daily::site::diary');
    }

    public function delete(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $this->ownedByCompany($authUser, $dailySiteDiary) && $authUser->can('delete_daily::site::diary');
    }

    public function restore(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $this->ownedByCompany($authUser, $dailySiteDiary) && $authUser->can('restore_daily::site::diary');
    }

    public function forceDelete(AuthUser $authUser, DailySiteDiary $dailySiteDiary): bool
    {
        return $this->ownedByCompany($authUser, $dailySiteDiary) && $authUser->can('force_delete_daily::site::diary');
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
        return $this->ownedByCompany($authUser, $dailySiteDiary) && $authUser->can('replicate_daily::site::diary');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_daily::site::diary');
    }
}