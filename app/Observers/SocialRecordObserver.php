<?php

namespace App\Observers;

use App\Models\CdeActivityLog;
use App\Models\SocialRecord;
use App\Models\User;
use App\Services\ModuleNotificationService;

class SocialRecordObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(SocialRecord $record): void
    {
        if ($record->assigned_to) {
            $assignee = User::find($record->assigned_to);
            if ($assignee) {
                $this->notifications->notifyUser('social-record-created', $assignee, [
                    'record_number' => $record->record_number ?? '',
                    'title' => $record->title ?? '',
                    'category' => SocialRecord::$categories[$record->category] ?? $record->category ?? '',
                    'priority' => ucfirst($record->priority ?? 'normal'),
                    'project_name' => $record->project?->name ?? '',
                    'reported_by' => auth()->user()?->name ?? 'System',
                ], url("/app/social-records/{$record->id}"));
            }
        }
    }

    public function updated(SocialRecord $record): void
    {
        if (!$record->isDirty('status') && !$record->isDirty('assigned_to')) {
            return;
        }

        $vars = [
            'record_number' => $record->record_number ?? '',
            'title' => $record->title ?? '',
            'project_name' => $record->project?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/social-records/{$record->id}");

        if ($record->isDirty('status')) {
            $vars['status'] = SocialRecord::$statuses[$record->status] ?? ucfirst(str_replace('_', ' ', $record->status));

            CdeActivityLog::record(
                $record,
                'status_changed',
                "Social record '{$record->record_number}' status changed to '{$record->status}'",
                ['from' => $record->getOriginal('status'), 'to' => $record->status],
            );

            // Notify reporter on resolution
            if (in_array($record->status, ['resolved', 'closed'])) {
                $reporter = User::find($record->reported_by);
                if ($reporter && (int) $reporter->id !== (int) auth()->id()) {
                    $this->notifications->notifyUser('social-record-resolved', $reporter, $vars, $url);
                }
            }

            // Notify assignee on status change
            if ($record->assigned_to) {
                $assignee = User::find($record->assigned_to);
                if ($assignee && (int) $assignee->id !== (int) auth()->id()) {
                    $this->notifications->notifyUser('social-record-status-changed', $assignee, $vars, $url);
                }
            }
        }

        if ($record->isDirty('assigned_to') && $record->assigned_to) {
            $newAssignee = User::find($record->assigned_to);
            if ($newAssignee && (int) $newAssignee->id !== (int) auth()->id()) {
                $this->notifications->notifyUser('social-record-assigned', $newAssignee, $vars, $url);
            }
        }
    }

    public function deleted(SocialRecord $record): void
    {
        $reporter = User::find($record->reported_by);
        if ($reporter) {
            $this->notifications->notifyUser('social-record-deleted', $reporter, [
                'record_number' => $record->record_number ?? '',
                'title' => $record->title ?? '',
                'project_name' => $record->project?->name ?? '',
            ]);
        }
    }
}
