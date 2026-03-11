<?php

namespace App\Observers;

use App\Models\EquipmentAllocation;
use App\Models\User;
use App\Services\ModuleNotificationService;

class EquipmentAllocationObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(EquipmentAllocation $alloc): void
    {
        if (!$alloc->operator_id)
            return;

        $operator = User::find($alloc->operator_id);
        if (!$operator)
            return;

        $this->notifications->notifyUser('equipment-assigned', $operator, [
            'asset_name' => $alloc->asset?->name ?? $alloc->asset?->registration_number ?? '',
            'project_name' => $alloc->cdeProject?->name ?? '',
            'start_date' => $alloc->start_date?->format('M d, Y') ?? '',
            'end_date' => $alloc->end_date?->format('M d, Y') ?? 'Ongoing',
            'assigned_by' => auth()->user()?->name ?? 'System',
        ], url("/app/equipment-allocations/{$alloc->id}"));
    }

    public function updated(EquipmentAllocation $alloc): void
    {
        if (!$alloc->isDirty('status'))
            return;

        if ($alloc->status === 'returned' || $alloc->status === 'completed') {
            $this->notifications->notifyCompanyAdmins(
                'equipment-returned',
                $alloc->company_id,
                [
                    'asset_name' => $alloc->asset?->name ?? $alloc->asset?->registration_number ?? '',
                    'project_name' => $alloc->cdeProject?->name ?? '',
                    'returned_by' => auth()->user()?->name ?? 'System',
                ],
                url("/app/equipment-allocations/{$alloc->id}")
            );
        }
    }
}
