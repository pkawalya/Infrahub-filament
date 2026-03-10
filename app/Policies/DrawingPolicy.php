<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Drawing;
use Illuminate\Auth\Access\HandlesAuthorization;

class DrawingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_drawing');
    }

    public function view(AuthUser $authUser, Drawing $drawing): bool
    {
        return $authUser->can('view_drawing');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_drawing');
    }

    public function update(AuthUser $authUser, Drawing $drawing): bool
    {
        return $authUser->can('update_drawing');
    }

    public function delete(AuthUser $authUser, Drawing $drawing): bool
    {
        return $authUser->can('delete_drawing');
    }

    public function restore(AuthUser $authUser, Drawing $drawing): bool
    {
        return $authUser->can('restore_drawing');
    }

    public function forceDelete(AuthUser $authUser, Drawing $drawing): bool
    {
        return $authUser->can('force_delete_drawing');
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
        return $authUser->can('replicate_drawing');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_drawing');
    }

}