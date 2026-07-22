<?php

namespace App\Observers;

use App\Models\ProjectSuggestion;
use App\Models\User;
use App\Models\CdeActivityLog;
use App\Services\ModuleNotificationService;

class ProjectSuggestionObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(ProjectSuggestion $suggestion): void
    {
        $project = $suggestion->project;
        if (!$project) {
            return;
        }

        $vars = [
            'category' => ProjectSuggestion::$categories[$suggestion->category] ?? $suggestion->category ?? '',
            'author' => $suggestion->is_anonymous ? 'Anonymous' : ($suggestion->author?->name ?? 'Unknown'),
            'project_name' => $project->name ?? '',
        ];

        $url = url("/app/project-suggestions/{$suggestion->id}");

        $this->notifications->notifyProjectTeam('project-suggestion-created', $project->id, $vars, $url, $suggestion->author_id);

        CdeActivityLog::log('suggestion_created', $suggestion, $vars);
    }

    public function updated(ProjectSuggestion $suggestion): void
    {
        if ($suggestion->isDirty('status')) {
            $old = $suggestion->getOriginal('status');
            $new = $suggestion->status;

            $vars = [
                'old_status' => ProjectSuggestion::$statuses[$old] ?? $old,
                'new_status' => ProjectSuggestion::$statuses[$new] ?? $new,
                'project_name' => $suggestion->project?->name ?? '',
            ];

            $author = $suggestion->is_anonymous ? null : $suggestion->author;
            if ($author) {
                $this->notifications->notifyUser("project-suggestion-{$new}", $author, $vars, url("/app/project-suggestions/{$suggestion->id}"));
            }

            CdeActivityLog::log('suggestion_status_changed', $suggestion, $vars);
        }

        if ($suggestion->isDirty('admin_response') && $suggestion->admin_response) {
            $vars = [
                'admin_response' => $suggestion->admin_response,
                'project_name' => $suggestion->project?->name ?? '',
            ];

            $author = $suggestion->is_anonymous ? null : $suggestion->author;
            if ($author) {
                $this->notifications->notifyUser('project-suggestion-responded', $author, $vars, url("/app/project-suggestions/{$suggestion->id}"));
            }

            CdeActivityLog::log('suggestion_responded', $suggestion, $vars);
        }
    }

    public function deleted(ProjectSuggestion $suggestion): void
    {
        CdeActivityLog::log('suggestion_deleted', $suggestion, [
            'project_name' => $suggestion->project?->name ?? '',
        ]);
    }
}
