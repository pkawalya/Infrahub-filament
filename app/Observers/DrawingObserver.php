<?php

namespace App\Observers;

use App\Models\Drawing;
use App\Models\User;
use App\Services\ModuleNotificationService;

class DrawingObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function updated(Drawing $drawing): void
    {
        if (!$drawing->isDirty('status'))
            return;

        $vars = [
            'drawing_number' => $drawing->drawing_number,
            'drawing_title' => $drawing->title,
            'discipline' => Drawing::$disciplines[$drawing->discipline] ?? $drawing->discipline,
            'revision' => $drawing->current_revision,
            'project_name' => $drawing->project?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/drawings/{$drawing->id}");

        match ($drawing->status) {
            'for_review' => $this->notifications->notifyProjectTeam(
                'drawing-submitted-review',
                $drawing->cde_project_id,
                $vars,
                $url,
                auth()->id()
            ),
            'approved' => $this->notifyDrawnBy($drawing, 'drawing-approved', $vars, $url),
            'ifc' => $this->notifications->notifyProjectTeam(
                'drawing-issued-construction',
                $drawing->cde_project_id,
                $vars,
                $url,
                auth()->id()
            ),
            default => null,
        };
    }

    protected function notifyDrawnBy(Drawing $drawing, string $slug, array $vars, string $url): void
    {
        $drawnBy = $drawing->drawn_by ? User::find($drawing->drawn_by) : null;
        if ($drawnBy && $drawnBy->id !== auth()->id()) {
            $this->notifications->notifyUser($slug, $drawnBy, $vars, $url);
        }
    }
}
