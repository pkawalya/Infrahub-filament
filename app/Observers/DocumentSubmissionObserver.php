<?php

namespace App\Observers;

use App\Models\DocumentSubmission;
use App\Models\User;
use App\Models\CdeActivityLog;
use App\Services\ModuleNotificationService;

class DocumentSubmissionObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(DocumentSubmission $submission): void
    {
        if (!$submission->submitter) {
            return;
        }

        $this->notifications->notifyUser('document-submission-created', $submission->submitter, [
            'submission_title' => $submission->title ?? '',
            'discipline' => DocumentSubmission::$disciplines[$submission->discipline] ?? $submission->discipline ?? '',
            'stage' => DocumentSubmission::$stages[$submission->stage] ?? $submission->stage ?? '',
            'due_date' => $submission->due_date?->format('M d, Y') ?? 'No deadline',
            'project_name' => $submission->project?->name ?? '',
        ], url("/app/document-submissions/{$submission->id}"));
    }

    public function updated(DocumentSubmission $submission): void
    {
        if (!$submission->isDirty('status')) {
            return;
        }

        $newStatus = $submission->status;
        $project = $submission->project;

        $vars = [
            'submission_title' => $submission->title ?? '',
            'status' => DocumentSubmission::$statuses[$newStatus] ?? ucfirst(str_replace('_', ' ', $newStatus)),
            'project_name' => $project?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/document-submissions/{$submission->id}");

        // Log the status change
        CdeActivityLog::record(
            $submission,
            'status_changed',
            "Document submission '{$submission->title}' status changed to '{$newStatus}'",
            ['from' => $submission->getOriginal('status'), 'to' => $newStatus],
        );

        // Notify submitter on approval/rejection
        if (in_array($newStatus, ['approved', 'rejected', 'submitted'])) {
            $submitter = $submission->submitter;
            if ($submitter && (int) $submitter->id !== (int) auth()->id()) {
                $this->notifications->notifyUser("document-submission-{$newStatus}", $submitter, $vars, $url);
            }
        }

        // Notify project team when submitted for review
        if ($newStatus === 'submitted' && $project) {
            $this->notifications->notifyProjectTeam('document-submission-submitted', $project->id, $vars, $url, $submission->submitted_by);
        }
    }

    public function deleted(DocumentSubmission $submission): void
    {
        $submitter = $submission->submitter;
        if ($submitter) {
            $this->notifications->notifyUser('document-submission-deleted', $submitter, [
                'submission_title' => $submission->title ?? '',
                'project_name' => $submission->project?->name ?? '',
            ]);
        }
    }
}
