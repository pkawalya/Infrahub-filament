<?php

namespace App\Observers;

use App\Models\CdeDocument;
use App\Models\User;
use App\Models\CdeActivityLog;
use App\Services\ModuleNotificationService;

class DocumentObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(CdeDocument $document): void
    {
        $project = $document->project;
        if (!$project) {
            return;
        }

        $this->notifications->notifyProjectTeam('document-created', $project->id, [
            'document_number' => $document->document_number ?? '',
            'document_title' => $document->title ?? '',
            'project_name' => $project->name ?? '',
            'uploaded_by' => auth()->user()?->name ?? 'System',
        ], url("/app/cde-projects/{$project->id}/documents/{$document->id}"), auth()->id());
    }

    public function updated(CdeDocument $document): void
    {
        if (!$document->isDirty('status')) {
            return;
        }

        $fromStatus = $document->getOriginal('status');
        $newStatus = $document->status;
        $project = $document->project;

        $vars = [
            'document_number' => $document->document_number ?? '',
            'document_title' => $document->title ?? '',
            'project_name' => $project?->name ?? '',
            'from_status' => CdeDocument::$statuses[$fromStatus] ?? $fromStatus,
            'to_status' => CdeDocument::$statuses[$newStatus] ?? $newStatus,
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = $project ? url("/app/cde-projects/{$project->id}/documents/{$document->id}") : null;

        // Log the status change
        CdeActivityLog::record(
            $document,
            'status_changed',
            "Document '{$document->document_number}' status changed from '{$fromStatus}' to '{$newStatus}'",
            ['from' => $fromStatus, 'to' => $newStatus],
        );

        // Notify project team on key transitions
        $notifySlug = "document-{$newStatus}";
        if ($project) {
            $this->notifications->notifyProjectTeam($notifySlug, $project->id, $vars, $url, auth()->id());

            // Notify linked RFI submitters
            $linkedRfis = $document->rfis()->whereNotNull('assigned_to')->get();
            foreach ($linkedRfis as $rfi) {
                $assignee = $rfi->assignee;
                if ($assignee && (int) $assignee->id !== (int) auth()->id()) {
                    $this->notifications->notifyUser("document-{$newStatus}-linked-rfi", $assignee, $vars, $url);
                }
            }

            // Notify linked submittal reviewers
            $linkedSubmittals = $document->submittals()->whereNotNull('reviewer_id')->get();
            foreach ($linkedSubmittals as $submittal) {
                $reviewer = $submittal->reviewer;
                if ($reviewer && (int) $reviewer->id !== (int) auth()->id()) {
                    $this->notifications->notifyUser("document-{$newStatus}-linked-submittal", $reviewer, $vars, $url);
                }
            }
        }

        // ── Revision lifecycle: auto-create new draft ──
        if ($newStatus === 'revision') {
            $this->createRevisionDraft($document, $fromStatus);
        }
    }

    public function deleted(CdeDocument $document): void
    {
        $project = $document->project;
        if (!$project) {
            return;
        }

        $this->notifications->notifyProjectTeam('document-deleted', $project->id, [
            'document_number' => $document->document_number ?? '',
            'document_title' => $document->title ?? '',
            'project_name' => $project->name ?? '',
        ], null, auth()->id());
    }

    /**
     * When a document transitions to 'revision', create a new draft
     * as the next version linked to this parent.
     */
    protected function createRevisionDraft(CdeDocument $document, string $fromStatus): void
    {
        $nextRevision = $this->bumpRevision($document->revision);

        $draft = CdeDocument::create([
            'company_id' => $document->company_id,
            'cde_project_id' => $document->cde_project_id,
            'cde_folder_id' => $document->cde_folder_id,
            'document_number' => $document->document_number,
            'title' => $document->title,
            'description' => $document->description,
            'discipline' => $document->discipline,
            'type' => $document->type,
            'status' => 'wip',
            'revision' => $nextRevision,
            'previous_version_id' => $document->id,
            'version_notes' => "Auto-created from revision request (was: {$fromStatus})",
            'uploaded_by' => auth()->id(),
        ]);

        CdeActivityLog::record(
            $draft,
            'created',
            "Auto-created revision {$nextRevision} of document '{$document->document_number}' from '{$fromStatus}' transition",
        );
    }

    protected function bumpRevision(?string $revision): string
    {
        if (!$revision || !preg_match('/^([A-Za-z]*)(\d+)$/', $revision, $m)) {
            // If no revision or non-standard format, start at P01
            return 'P01';
        }

        $prefix = $m[1];
        $number = (int) $m[2] + 1;

        return $prefix . str_pad((string) $number, 2, '0', STR_PAD_LEFT);
    }
}
