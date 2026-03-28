<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EquipmentAllocation;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class EquipmentAllocationPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_equipment::allocation');
    }

    public function view(AuthUser $authUser, EquipmentAllocation $equipmentAllocation): bool
    {
        return $this->ownedByCompany($authUser, $equipmentAllocation) && $authUser->can('view_equipment::allocation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_equipment::allocation');
    }

    public function update(AuthUser $authUser, EquipmentAllocation $equipmentAllocation): bool
    {
        return $this->ownedByCompany($authUser, $equipmentAllocation) && $authUser->can('update_equipment::allocation');
    }

    public function delete(AuthUser $authUser, EquipmentAllocation $equipmentAllocation): bool
    {
        return $this->ownedByCompany($authUser, $equipmentAllocation) && $authUser->can('delete_equipment::allocation');
    }

    public function restore(AuthUser $authUser, EquipmentAllocation $equipmentAllocation): bool
    {
        return $this->ownedByCompany($authUser, $equipmentAllocation) && $authUser->can('restore_equipment::allocation');
    }

    public function forceDelete(AuthUser $authUser, EquipmentAllocation $equipmentAllocation): bool
    {
        return $this->ownedByCompany($authUser, $equipmentAllocation) && $authUser->can('force_delete_equipment::allocation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_equipment::allocation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_equipment::allocation');
    }

    public function replicate(AuthUser $authUser, EquipmentAllocation $equipmentAllocation): bool
    {
        return $this->ownedByCompany($authUser, $equipmentAllocation) && $authUser->can('replicate_equipment::allocation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_equipment::allocation');
    }
}