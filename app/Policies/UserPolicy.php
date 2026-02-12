<?php

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_company::user');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('view_company::user');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_company::user');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('update_company::user');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('delete_company::user');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('restore_company::user');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_company::user');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_company::user');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_company::user');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('replicate_company::user');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_company::user');
    }

}