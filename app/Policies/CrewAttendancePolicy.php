<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CrewAttendance;
use App\Policies\Concerns\EnforcesCompanyOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CrewAttendancePolicy
{
    use HandlesAuthorization, EnforcesCompanyOwnership;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_crew::attendance');
    }

    public function view(AuthUser $authUser, CrewAttendance $crewAttendance): bool
    {
        return $this->ownedByCompany($authUser, $crewAttendance) && $authUser->can('view_crew::attendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_crew::attendance');
    }

    public function update(AuthUser $authUser, CrewAttendance $crewAttendance): bool
    {
        return $this->ownedByCompany($authUser, $crewAttendance) && $authUser->can('update_crew::attendance');
    }

    public function delete(AuthUser $authUser, CrewAttendance $crewAttendance): bool
    {
        return $this->ownedByCompany($authUser, $crewAttendance) && $authUser->can('delete_crew::attendance');
    }

    public function restore(AuthUser $authUser, CrewAttendance $crewAttendance): bool
    {
        return $this->ownedByCompany($authUser, $crewAttendance) && $authUser->can('restore_crew::attendance');
    }

    public function forceDelete(AuthUser $authUser, CrewAttendance $crewAttendance): bool
    {
        return $this->ownedByCompany($authUser, $crewAttendance) && $authUser->can('force_delete_crew::attendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_crew::attendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_crew::attendance');
    }

    public function replicate(AuthUser $authUser, CrewAttendance $crewAttendance): bool
    {
        return $this->ownedByCompany($authUser, $crewAttendance) && $authUser->can('replicate_crew::attendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_crew::attendance');
    }
}