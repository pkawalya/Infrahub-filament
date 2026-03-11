<?php

namespace App\Observers;

use App\Models\MaterialRequisition;
use App\Models\User;
use App\Services\ModuleNotificationService;

class MaterialRequisitionObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function updated(MaterialRequisition $mr): void
    {
        if (!$mr->isDirty('status'))
            return;

        $vars = [
            'requisition_number' => $mr->requisition_number ?? '',
            'purpose' => $mr->purpose ?? '',
            'priority' => ucfirst($mr->priority ?? 'normal'),
            'required_date' => $mr->required_date?->format('M d, Y') ?? '',
            'project_name' => $mr->cdeProject?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/material-requisitions/{$mr->id}");

        match ($mr->status) {
            'submitted', 'pending_approval' => $this->notifications->notifyCompanyAdmins(
                'requisition-submitted',
                $mr->company_id,
                $vars,
                $url
            ),
            'approved' => $this->notifyRequester($mr, 'requisition-approved', $vars, $url),
            'rejected' => $this->notifyRequester($mr, 'requisition-rejected', $vars, $url),
            'issued' => $this->notifyRequester($mr, 'requisition-issued', $vars, $url),
            default => null,
        };
    }

    protected function notifyRequester(MaterialRequisition $mr, string $slug, array $vars, string $url): void
    {
        $requester = User::find($mr->requester_id);
        if ($requester && $requester->id !== auth()->id()) {
            $this->notifications->notifyUser($slug, $requester, $vars, $url);
        }
    }
}
