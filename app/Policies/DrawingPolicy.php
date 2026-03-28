<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Drawing;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DrawingPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_drawing');
    }

    public function view(AuthUser $authUser, Drawing $drawing): bool
    {
        return $this->ownedByCompany($authUser, $drawing) && $authUser->can('view_drawing');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_drawing');
    }

    public function update(AuthUser $authUser, Drawing $drawing): bool
    {
        return $this->ownedByCompany($authUser, $drawing) && $authUser->can('update_drawing');
    }

    public function delete(AuthUser $authUser, Drawing $drawing): bool
    {
        return $this->ownedByCompany($authUser, $drawing) && $authUser->can('delete_drawing');
    }

    public function restore(AuthUser $authUser, Drawing $drawing): bool
    {
        return $this->ownedByCompany($authUser, $drawing) && $authUser->can('restore_drawing');
    }

    public function forceDelete(AuthUser $authUser, Drawing $drawing): bool
    {
        return $this->ownedByCompany($authUser, $drawing) && $authUser->can('force_delete_drawing');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_drawing');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_drawing');
    }

    public function replicate(AuthUser $authUser, Drawing $drawing): bool
    {
        return $this->ownedByCompany($authUser, $drawing) && $authUser->can('replicate_drawing');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_drawing');
    }
}