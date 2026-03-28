<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\WorkOrder;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WorkOrderPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_work::order');
    }

    public function view(AuthUser $authUser, WorkOrder $workOrder): bool
    {
        return $this->ownedByCompany($authUser, $workOrder) && $authUser->can('view_work::order');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_work::order');
    }

    public function update(AuthUser $authUser, WorkOrder $workOrder): bool
    {
        return $this->ownedByCompany($authUser, $workOrder) && $authUser->can('update_work::order');
    }

    public function delete(AuthUser $authUser, WorkOrder $workOrder): bool
    {
        return $this->ownedByCompany($authUser, $workOrder) && $authUser->can('delete_work::order');
    }

    public function restore(AuthUser $authUser, WorkOrder $workOrder): bool
    {
        return $this->ownedByCompany($authUser, $workOrder) && $authUser->can('restore_work::order');
    }

    public function forceDelete(AuthUser $authUser, WorkOrder $workOrder): bool
    {
        return $this->ownedByCompany($authUser, $workOrder) && $authUser->can('force_delete_work::order');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_work::order');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_work::order');
    }

    public function replicate(AuthUser $authUser, WorkOrder $workOrder): bool
    {
        return $this->ownedByCompany($authUser, $workOrder) && $authUser->can('replicate_work::order');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_work::order');
    }
}