<?php

namespace App\Observers;

use App\Models\CdeActivityLog;
use App\Models\Tender;
use App\Models\User;
use App\Services\ModuleNotificationService;

class TenderObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(Tender $tender): void
    {
        if ($tender->assigned_to) {
            $assignee = User::find($tender->assigned_to);
            if ($assignee) {
                $this->notifications->notifyUser('tender-created', $assignee, [
                    'tender_reference' => $tender->reference ?? '',
                    'tender_title' => $tender->title ?? '',
                    'client_name' => $tender->client_name ?? '',
                    'estimated_value' => number_format($tender->estimated_value ?? 0, 2),
                    'submission_deadline' => $tender->submission_deadline?->format('M d, Y') ?? 'No deadline',
                    'created_by' => auth()->user()?->name ?? 'System',
                ], url("/app/tenders/{$tender->id}"));
            }
        }
    }

    public function updated(Tender $tender): void
    {
        if (!$tender->isDirty('status')) {
            return;
        }

        $vars = [
            'tender_reference' => $tender->reference ?? '',
            'tender_title' => $tender->title ?? '',
            'status' => Tender::$statuses[$tender->status] ?? ucfirst(str_replace('_', ' ', $tender->status)),
            'client_name' => $tender->client_name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/tenders/{$tender->id}");

        CdeActivityLog::record(
            $tender,
            'status_changed',
            "Tender '{$tender->reference}' status changed to '{$tender->status}'",
            ['from' => $tender->getOriginal('status'), 'to' => $tender->status],
        );

        // Notify assignee on key transitions
        if ($tender->assigned_to) {
            $assignee = User::find($tender->assigned_to);
            if ($assignee && (int) $assignee->id !== (int) auth()->id()) {
                $this->notifications->notifyUser("tender-{$tender->status}", $assignee, $vars, $url);
            }
        }

        // Notify creator on award/loss
        if (in_array($tender->status, ['awarded', 'lost'])) {
            $creator = User::find($tender->created_by);
            if ($creator && (int) $creator->id !== (int) auth()->id()) {
                $this->notifications->notifyUser("tender-{$tender->status}", $creator, $vars, $url);
            }
        }
    }

    public function deleted(Tender $tender): void
    {
        $assignee = $tender->assigned_to ? User::find($tender->assigned_to) : null;
        if ($assignee) {
            $this->notifications->notifyUser('tender-deleted', $assignee, [
                'tender_reference' => $tender->reference ?? '',
                'tender_title' => $tender->title ?? '',
            ]);
        }
    }
}
