<?php

namespace App\Observers;

use App\Models\ChangeOrder;
use App\Models\User;
use App\Services\ModuleNotificationService;

class ChangeOrderObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(ChangeOrder $co): void
    {
        // Notify project team about new change order
        $this->notifications->notifyProjectTeam(
            'change-order-created',
            $co->cde_project_id,
            [
                'co_reference' => $co->reference,
                'co_title' => $co->title,
                'co_type' => ChangeOrder::$types[$co->type] ?? $co->type,
                'estimated_cost' => number_format($co->estimated_cost, 2),
                'initiated_by' => $co->initiated_by ?? 'Unknown',
            ],
            url("/app/change-orders/{$co->id}"),
            auth()->id(),
        );
    }

    public function updated(ChangeOrder $co): void
    {
        if ($co->isDirty('status')) {
            $slug = match ($co->status) {
                'approved' => 'change-order-approved',
                'rejected' => 'change-order-rejected',
                'submitted' => 'change-order-submitted',
                default => null,
            };

            if ($slug) {
                // Notify the submitter
                $submitter = $co->submitted_by ? User::find($co->submitted_by) : null;
                if ($submitter && $submitter->id !== auth()->id()) {
                    $this->notifications->notifyUser($slug, $submitter, [
                        'co_reference' => $co->reference,
                        'co_title' => $co->title,
                        'approved_cost' => $co->approved_cost ? number_format($co->approved_cost, 2) : 'N/A',
                        'approval_notes' => $co->approval_notes ?? '',
                        'rejection_reason' => $co->rejection_reason ?? '',
                        'actioned_by' => auth()->user()?->name ?? 'System',
                    ], url("/app/change-orders/{$co->id}"));
                }

                // Notify company admins on approval
                if ($co->status === 'approved' && $co->company_id) {
                    $this->notifications->notifyCompanyAdmins('change-order-approved', $co->company_id, [
                        'co_reference' => $co->reference,
                        'co_title' => $co->title,
                        'approved_cost' => number_format($co->approved_cost ?? $co->estimated_cost, 2),
                        'project_name' => $co->project?->name ?? '',
                    ], url("/app/change-orders/{$co->id}"));
                }
            }
        }
    }
}
