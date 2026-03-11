<?php

namespace App\Observers;

use App\Models\Submittal;
use App\Models\User;
use App\Services\ModuleNotificationService;

class SubmittalObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(Submittal $submittal): void
    {
        // Notify reviewer when a new submittal is created
        if (!$submittal->reviewer_id)
            return;

        $reviewer = User::find($submittal->reviewer_id);
        if (!$reviewer)
            return;

        $this->notifications->notifyUser('submittal-submitted', $reviewer, [
            'submittal_number' => $submittal->submittal_number ?? '',
            'submittal_title' => $submittal->title ?? '',
            'type' => ucfirst($submittal->type ?? ''),
            'project_name' => $submittal->cdeProject?->name ?? '',
            'submitted_by' => auth()->user()?->name ?? 'System',
        ], url("/app/submittals/{$submittal->id}"));
    }

    public function updated(Submittal $submittal): void
    {
        if (!$submittal->isDirty('status'))
            return;

        $vars = [
            'submittal_number' => $submittal->submittal_number ?? '',
            'submittal_title' => $submittal->title ?? '',
            'status' => ucfirst(str_replace('_', ' ', $submittal->status)),
            'project_name' => $submittal->cdeProject?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
            'review_comments' => $submittal->review_comments ?? '',
        ];

        $url = url("/app/submittals/{$submittal->id}");

        // Notify the submitter of the review outcome
        $submitter = User::find($submittal->submitted_by);
        if (!$submitter || $submitter->id === auth()->id())
            return;

        match ($submittal->status) {
            'approved', 'approved_as_noted' => $this->notifications->notifyUser('submittal-approved', $submitter, $vars, $url),
            'rejected', 'revise_resubmit' => $this->notifications->notifyUser('submittal-rejected', $submitter, $vars, $url),
            default => null,
        };
    }
}
