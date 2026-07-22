<?php

namespace App\Observers;

use App\Models\Ncr;
use App\Models\User;
use App\Services\ModuleNotificationService;

class NcrObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(Ncr $ncr): void
    {
        if ($ncr->assigned_to) {
            $assignee = User::find($ncr->assigned_to);
            if ($assignee) {
                $this->notifications->notifyUser('ncr-created', $assignee, [
                    'ncr_number' => $ncr->ncr_number ?? '',
                    'ncr_title' => $ncr->title ?? '',
                    'severity' => ucfirst($ncr->severity ?? ''),
                    'type' => Ncr::$types[$ncr->type] ?? $ncr->type ?? '',
                    'due_date' => $ncr->due_date?->format('M d, Y') ?? 'No deadline',
                    'project_name' => $ncr->project?->name ?? '',
                    'reported_by' => auth()->user()?->name ?? 'System',
                ], url("/app/ncrs/{$ncr->id}"));
            }
        }

        $reporter = User::find($ncr->reported_by);
        if ($reporter && (!$ncr->assigned_to || (int) $ncr->assigned_to !== (int) $ncr->reported_by)) {
            $this->notifications->notifyUser('ncr-created-reporter', $reporter, [
                'ncr_number' => $ncr->ncr_number ?? '',
                'ncr_title' => $ncr->title ?? '',
                'project_name' => $ncr->project?->name ?? '',
            ], url("/app/ncrs/{$ncr->id}"));
        }
    }

    public function updated(Ncr $ncr): void
    {
        if (!$ncr->isDirty('status') && !$ncr->isDirty('assigned_to')) {
            return;
        }

        $vars = [
            'ncr_number' => $ncr->ncr_number ?? '',
            'ncr_title' => $ncr->title ?? '',
            'project_name' => $ncr->project?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/ncrs/{$ncr->id}");

        // ── Notify on status transitions ──
        if ($ncr->isDirty('status')) {
            $newStatus = $ncr->status;
            $vars['status'] = Ncr::$statuses[$newStatus] ?? ucfirst(str_replace('_', ' ', $newStatus));

            if (in_array($newStatus, ['investigating', 'corrective_action', 'verified', 'closed'])) {
                $assignee = $ncr->assigned_to ? User::find($ncr->assigned_to) : null;
                if ($assignee && (int) $assignee->id !== (int) auth()->id()) {
                    $this->notifications->notifyUser("ncr-{$newStatus}-assignee", $assignee, $vars, $url);
                }

                $reporter = User::find($ncr->reported_by);
                if ($reporter && (int) $reporter->id !== (int) auth()->id()) {
                    $this->notifications->notifyUser("ncr-{$newStatus}", $reporter, $vars, $url);
                }
            }

            if ($newStatus === 'closed') {
                $project = $ncr->project;
                if ($project) {
                    $this->notifications->notifyProjectTeam('ncr-closed', $project->id, $vars, $url, auth()->id());
                }
            }
        }

        // ── Notify on reassignment ──
        if ($ncr->isDirty('assigned_to') && $ncr->assigned_to) {
            $newAssignee = User::find($ncr->assigned_to);
            if ($newAssignee && (int) $newAssignee->id !== (int) auth()->id()) {
                $this->notifications->notifyUser('ncr-assigned', $newAssignee, $vars, $url);
            }
        }
    }

    public function deleted(Ncr $ncr): void
    {
        $vars = [
            'ncr_number' => $ncr->ncr_number ?? '',
            'ncr_title' => $ncr->title ?? '',
            'project_name' => $ncr->project?->name ?? '',
        ];

        $reporter = User::find($ncr->reported_by);
        if ($reporter) {
            $this->notifications->notifyUser('ncr-deleted', $reporter, $vars);
        }

        if ($ncr->assigned_to && (int) $ncr->assigned_to !== (int) $ncr->reported_by) {
            $assignee = User::find($ncr->assigned_to);
            if ($assignee) {
                $this->notifications->notifyUser('ncr-deleted', $assignee, $vars);
            }
        }
    }
}
