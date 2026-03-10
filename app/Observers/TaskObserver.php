<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\User;
use App\Services\ModuleNotificationService;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(Task $task): void
    {
        if (!$task->assigned_to)
            return;

        $assignee = User::find($task->assigned_to);
        if (!$assignee)
            return;

        $this->notifications->notifyUser('task-assigned', $assignee, [
            'task_title' => $task->title,
            'task_priority' => $task->priority ?? 'Normal',
            'task_due_date' => $task->end_date?->format('M d, Y') ?? 'No deadline',
            'project_name' => $task->project?->name ?? '',
            'assigned_by' => auth()->user()?->name ?? 'System',
        ], url("/app/tasks/{$task->id}"));
    }

    public function updated(Task $task): void
    {
        // Notify on status change to completed
        if ($task->isDirty('status') && $task->status === 'completed') {
            $creator = $task->creator ?? User::find($task->created_by);
            if ($creator && $creator->id !== auth()->id()) {
                $this->notifications->notifyUser('task-completed', $creator, [
                    'task_title' => $task->title,
                    'completed_by' => auth()->user()?->name ?? 'System',
                    'project_name' => $task->project?->name ?? '',
                ], url("/app/tasks/{$task->id}"));
            }
        }

        // Notify on reassignment
        if ($task->isDirty('assigned_to') && $task->assigned_to) {
            $newAssignee = User::find($task->assigned_to);
            if ($newAssignee && $newAssignee->id !== auth()->id()) {
                $this->notifications->notifyUser('task-assigned', $newAssignee, [
                    'task_title' => $task->title,
                    'task_priority' => $task->priority ?? 'Normal',
                    'task_due_date' => $task->end_date?->format('M d, Y') ?? 'No deadline',
                    'project_name' => $task->project?->name ?? '',
                    'assigned_by' => auth()->user()?->name ?? 'System',
                ], url("/app/tasks/{$task->id}"));
            }
        }
    }
}
