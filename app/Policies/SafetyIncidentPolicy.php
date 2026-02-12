<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SafetyIncident;
use Illuminate\Auth\Access\HandlesAuthorization;

class SafetyIncidentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_safety::incident');
    }

    public function view(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('view_safety::incident');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_safety::incident');
    }

    public function update(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('update_safety::incident');
    }

    public function delete(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('delete_safety::incident');
    }

    public function restore(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('restore_safety::incident');
    }

    public function forceDelete(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('force_delete_safety::incident');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_safety::incident');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_safety::incident');
    }

    public function replicate(AuthUser $authUser, SafetyIncident $safetyIncident): bool
    {
        return $authUser->can('replicate_safety::incident');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_safety::incident');
    }

}