<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\EquipmentFuelLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentFuelLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_equipment::fuel::log');
    }

    public function view(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $authUser->can('view_equipment::fuel::log');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_equipment::fuel::log');
    }

    public function update(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $authUser->can('update_equipment::fuel::log');
    }

    public function delete(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $authUser->can('delete_equipment::fuel::log');
    }

    public function restore(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $authUser->can('restore_equipment::fuel::log');
    }

    public function forceDelete(AuthUser $authUser, EquipmentFuelLog $equipmentFuelLog): bool
    {
        return $authUser->can('force_delete_equipment::fuel::log');
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
        return $authUser->can('replicate_equipment::fuel::log');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_equipment::fuel::log');
    }

}