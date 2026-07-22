<?php

namespace App\Observers;

use App\Models\CdeActivityLog;
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
        // Derive project from the linked contract
        $project = $co->contract?->primaryProject();
        if (!$project) {
            return;
        }

        $this->notifications->notifyProjectTeam(
            'change-order-created',
            $project->id,
            [
                'co_reference' => $co->co_number,
                'co_title' => $co->title,
                'amount' => number_format($co->amount ?? 0, 2),
                'requested_by' => $co->requester?->name ?? 'Unknown',
            ],
            url("/app/change-orders/{$co->id}"),
            auth()->id(),
        );
    }

    public function updated(ChangeOrder $co): void
    {
        if ($co->isDirty('status')) {
            CdeActivityLog::record(
                $co,
                'status_changed',
                "Change order '{$co->co_number}' status changed to '{$co->status}'",
                ['from' => $co->getOriginal('status'), 'to' => $co->status],
            );

            $slug = match ($co->status) {
                'approved' => 'change-order-approved',
                'rejected' => 'change-order-rejected',
                'submitted' => 'change-order-submitted',
                default => null,
            };

            if ($slug) {
                // Notify the requester
                $requester = $co->requested_by ? User::find($co->requested_by) : null;
                if ($requester && $requester->id !== auth()->id()) {
                    $this->notifications->notifyUser($slug, $requester, [
                        'co_reference' => $co->co_number,
                        'co_title' => $co->title,
                        'amount' => $co->amount ? number_format($co->amount, 2) : 'N/A',
                        'actioned_by' => auth()->user()?->name ?? 'System',
                    ], url("/app/change-orders/{$co->id}"));
                }

                // Notify company admins on approval
                if ($co->status === 'approved' && $co->company_id) {
                    $project = $co->contract?->primaryProject();
                    $this->notifications->notifyCompanyAdmins('change-order-approved', $co->company_id, [
                        'co_reference' => $co->co_number,
                        'co_title' => $co->title,
                        'amount' => number_format($co->amount ?? 0, 2),
                        'project_name' => $project?->name ?? '',
                    ], url("/app/change-orders/{$co->id}"));
                }
            }
        }
    }
}
