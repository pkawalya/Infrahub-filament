<?php

namespace App\Observers;

use App\Models\WorkOrder;
use App\Models\User;
use App\Services\ModuleNotificationService;

class WorkOrderObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(WorkOrder $wo): void
    {
        if (!$wo->assigned_to)
            return;

        $assignee = User::find($wo->assigned_to);
        if (!$assignee)
            return;

        $this->notifications->notifyUser('work-order-assigned', $assignee, [
            'wo_number' => $wo->wo_number ?? '',
            'wo_title' => $wo->title ?? '',
            'priority' => ucfirst($wo->priority ?? 'normal'),
            'due_date' => $wo->due_date?->format('M d, Y') ?? 'No deadline',
            'project_name' => $wo->cdeProject?->name ?? '',
            'assigned_by' => auth()->user()?->name ?? 'System',
        ], url("/app/work-orders/{$wo->id}"));
    }

    public function updated(WorkOrder $wo): void
    {
        if (!$wo->isDirty('status'))
            return;

        $vars = [
            'wo_number' => $wo->wo_number ?? '',
            'wo_title' => $wo->title ?? '',
            'status' => ucfirst(str_replace('_', ' ', $wo->status)),
            'priority' => ucfirst($wo->priority ?? 'normal'),
            'project_name' => $wo->cdeProject?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/work-orders/{$wo->id}");

        match ($wo->status) {
            'approved' => $this->notifyCreator($wo, 'work-order-approved', $vars, $url),
            'completed' => $this->notifyCreator($wo, 'work-order-completed', $vars, $url),
            'in_progress' => $this->notifyCreator($wo, 'work-order-started', $vars, $url),
            default => null,
        };

        // Notify on reassignment
        if ($wo->isDirty('assigned_to') && $wo->assigned_to) {
            $newAssignee = User::find($wo->assigned_to);
            if ($newAssignee && $newAssignee->id !== auth()->id()) {
                $this->notifications->notifyUser('work-order-assigned', $newAssignee, $vars, $url);
            }
        }
    }

    protected function notifyCreator(WorkOrder $wo, string $slug, array $vars, string $url): void
    {
        $creator = User::find($wo->created_by);
        if ($creator && $creator->id !== auth()->id()) {
            $this->notifications->notifyUser($slug, $creator, $vars, $url);
        }
    }
}
