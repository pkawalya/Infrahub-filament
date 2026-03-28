<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChangeOrder;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ChangeOrderPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_change::order');
    }

    public function view(AuthUser $authUser, ChangeOrder $changeOrder): bool
    {
        return $this->ownedByCompany($authUser, $changeOrder) && $authUser->can('view_change::order');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_change::order');
    }

    public function update(AuthUser $authUser, ChangeOrder $changeOrder): bool
    {
        return $this->ownedByCompany($authUser, $changeOrder) && $authUser->can('update_change::order');
    }

    public function delete(AuthUser $authUser, ChangeOrder $changeOrder): bool
    {
        return $this->ownedByCompany($authUser, $changeOrder) && $authUser->can('delete_change::order');
    }

    public function restore(AuthUser $authUser, ChangeOrder $changeOrder): bool
    {
        return $this->ownedByCompany($authUser, $changeOrder) && $authUser->can('restore_change::order');
    }

    public function forceDelete(AuthUser $authUser, ChangeOrder $changeOrder): bool
    {
        return $this->ownedByCompany($authUser, $changeOrder) && $authUser->can('force_delete_change::order');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_change::order');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_change::order');
    }

    public function replicate(AuthUser $authUser, ChangeOrder $changeOrder): bool
    {
        return $this->ownedByCompany($authUser, $changeOrder) && $authUser->can('replicate_change::order');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_change::order');
    }
}