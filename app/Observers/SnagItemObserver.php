<?php

namespace App\Observers;

use App\Models\SnagItem;
use App\Models\User;
use App\Services\ModuleNotificationService;

class SnagItemObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(SnagItem $snag): void
    {
        if (!$snag->assigned_to)
            return;

        $assignee = User::find($snag->assigned_to);
        if (!$assignee)
            return;

        $this->notifications->notifyUser('snag-created', $assignee, [
            'snag_number' => $snag->snag_number ?? '',
            'snag_title' => $snag->title ?? '',
            'severity' => ucfirst($snag->severity ?? ''),
            'location' => $snag->location ?? '',
            'due_date' => $snag->due_date?->format('M d, Y') ?? 'No deadline',
            'project_name' => $snag->cdeProject?->name ?? '',
            'reported_by' => auth()->user()?->name ?? 'System',
        ], url("/app/snag-items/{$snag->id}"));
    }

    public function updated(SnagItem $snag): void
    {
        if (!$snag->isDirty('status'))
            return;

        $vars = [
            'snag_number' => $snag->snag_number ?? '',
            'snag_title' => $snag->title ?? '',
            'status' => ucfirst(str_replace('_', ' ', $snag->status)),
            'project_name' => $snag->cdeProject?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/snag-items/{$snag->id}");

        if ($snag->status === 'resolved' || $snag->status === 'closed') {
            // Notify the reporter
            $reporter = User::find($snag->reported_by);
            if ($reporter && $reporter->id !== auth()->id()) {
                $this->notifications->notifyUser('snag-resolved', $reporter, $vars, $url);
            }
        }

        // Notify on reassignment
        if ($snag->isDirty('assigned_to') && $snag->assigned_to) {
            $newAssignee = User::find($snag->assigned_to);
            if ($newAssignee && $newAssignee->id !== auth()->id()) {
                $this->notifications->notifyUser('snag-created', $newAssignee, $vars, $url);
            }
        }
    }
}
