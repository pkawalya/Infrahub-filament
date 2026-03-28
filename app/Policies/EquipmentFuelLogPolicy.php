<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\EquipmentFuelLog;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class EquipmentFuelLogPolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_equipment::fuel::log');
    }

    public function view(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $this->ownedByCompany($authUser, $equipmentFuelLog) && $authUser->can('view_equipment::fuel::log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_equipment::fuel::log');
    }

    public function update(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $this->ownedByCompany($authUser, $equipmentFuelLog) && $authUser->can('update_equipment::fuel::log');
    }

    public function delete(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $this->ownedByCompany($authUser, $equipmentFuelLog) && $authUser->can('delete_equipment::fuel::log');
    }

    public function restore(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $this->ownedByCompany($authUser, $equipmentFuelLog) && $authUser->can('restore_equipment::fuel::log');
    }

    public function forceDelete(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $this->ownedByCompany($authUser, $equipmentFuelLog) && $authUser->can('force_delete_equipment::fuel::log');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_equipment::fuel::log');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_equipment::fuel::log');
    }

    public function replicate(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $this->ownedByCompany($authUser, $equipmentFuelLog) && $authUser->can('replicate_equipment::fuel::log');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_equipment::fuel::log');
    }
}