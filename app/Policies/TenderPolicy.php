<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Tender;
use Illuminate\Auth\Access\HandlesAuthorization;

class TenderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_tender');
    }

    public function view(AuthUser $authUser, Tender $tender): bool
    {
        return $authUser->can('view_tender');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_tender');
    }

    public function update(AuthUser $authUser, Tender $tender): bool
    {
        return $authUser->can('update_tender');
    }

    public function delete(AuthUser $authUser, Tender $tender): bool
    {
        return $authUser->can('delete_tender');
    }

    public function restore(AuthUser $authUser, Tender $tender): bool
    {
        return $authUser->can('restore_tender');
    }

    public function forceDelete(AuthUser $authUser, Tender $tender): bool
    {
        return $authUser->can('force_delete_tender');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_tender');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_tender');
    }

    public function replicate(AuthUser $authUser, Tender $tender): bool
    {
        return $authUser->can('replicate_tender');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_tender');
    }

}