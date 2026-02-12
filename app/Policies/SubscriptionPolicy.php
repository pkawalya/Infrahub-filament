<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_subscription');
    }

    public function view(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('view_subscription');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_subscription');
    }

    public function update(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('update_subscription');
    }

    public function delete(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('delete_subscription');
    }

    public function restore(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('restore_subscription');
    }

    public function forceDelete(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('force_delete_subscription');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_subscription');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_subscription');
    }

    public function replicate(AuthUser $authUser, Subscription $subscription): bool
    {
        return $authUser->can('replicate_subscription');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_subscription');
    }

}