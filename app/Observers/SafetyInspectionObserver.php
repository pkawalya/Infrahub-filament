<?php

namespace App\Observers;

use App\Models\CdeActivityLog;
use App\Models\SafetyInspection;
use App\Models\User;
use App\Services\ModuleNotificationService;

class SafetyInspectionObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(SafetyInspection $inspection): void
    {
        $inspector = $inspection->inspector;
        if (!$inspector) {
            return;
        }

        $this->notifications->notifyUser('inspection-scheduled', $inspector, [
            'inspection_number' => $inspection->inspection_number ?? '',
            'inspection_title' => $inspection->title ?? '',
            'type' => $inspection->type ?? '',
            'scheduled_date' => $inspection->scheduled_date?->format('M d, Y H:i') ?? 'Unscheduled',
            'location' => $inspection->location ?? '',
            'project_name' => $inspection->project?->name ?? '',
            'scheduled_by' => auth()->user()?->name ?? 'System',
        ], url("/app/safety-inspections/{$inspection->id}"));
    }

    public function updated(SafetyInspection $inspection): void
    {
        if (!$inspection->isDirty('status')) {
            return;
        }

        $vars = [
            'inspection_number' => $inspection->inspection_number ?? '',
            'inspection_title' => $inspection->title ?? '',
            'status' => SafetyInspection::$statuses[$inspection->status] ?? ucfirst(str_replace('_', ' ', $inspection->status)),
            'score' => $inspection->score ?? '—',
            'project_name' => $inspection->project?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/safety-inspections/{$inspection->id}");

        CdeActivityLog::record(
            $inspection,
            'status_changed',
            "Inspection '{$inspection->inspection_number}' status changed to '{$inspection->status}'",
            ['from' => $inspection->getOriginal('status'), 'to' => $inspection->status],
        );

        if ($inspection->status === 'completed') {
            $project = $inspection->project;
            if ($project) {
                $this->notifications->notifyProjectTeam('inspection-completed', $project->id, $vars, $url, $inspection->inspector_id);
            }
        }

        $inspector = $inspection->inspector;
        if ($inspector && (int) $inspector->id !== (int) auth()->id()) {
            $this->notifications->notifyUser('inspection-status-changed', $inspector, $vars, $url);
        }
    }

    public function deleted(SafetyInspection $inspection): void
    {
        $inspector = $inspection->inspector;
        if ($inspector) {
            $this->notifications->notifyUser('inspection-deleted', $inspector, [
                'inspection_number' => $inspection->inspection_number ?? '',
                'inspection_title' => $inspection->title ?? '',
                'project_name' => $inspection->project?->name ?? '',
            ]);
        }
    }
}
