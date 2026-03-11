<?php

namespace App\Observers;

use App\Models\Rfi;
use App\Models\User;
use App\Services\ModuleNotificationService;

class RfiObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function created(Rfi $rfi): void
    {
        if (!$rfi->assigned_to)
            return;

        $assignee = User::find($rfi->assigned_to);
        if (!$assignee)
            return;

        $this->notifications->notifyUser('rfi-created', $assignee, [
            'rfi_number' => $rfi->rfi_number ?? '',
            'rfi_subject' => $rfi->subject ?? '',
            'priority' => ucfirst($rfi->priority ?? 'normal'),
            'due_date' => $rfi->due_date?->format('M d, Y') ?? 'No deadline',
            'project_name' => $rfi->cdeProject?->name ?? '',
            'raised_by' => auth()->user()?->name ?? 'System',
        ], url("/app/rfis/{$rfi->id}"));
    }

    public function updated(Rfi $rfi): void
    {
        if (!$rfi->isDirty('status'))
            return;

        $vars = [
            'rfi_number' => $rfi->rfi_number ?? '',
            'rfi_subject' => $rfi->subject ?? '',
            'status' => ucfirst(str_replace('_', ' ', $rfi->status)),
            'project_name' => $rfi->cdeProject?->name ?? '',
            'actioned_by' => auth()->user()?->name ?? 'System',
        ];

        $url = url("/app/rfis/{$rfi->id}");

        match ($rfi->status) {
            'answered' => $this->notifyRaiser($rfi, 'rfi-answered', $vars, $url),
            'closed' => $this->notifyRaiser($rfi, 'rfi-closed', $vars, $url),
            default => null,
        };
    }

    protected function notifyRaiser(Rfi $rfi, string $slug, array $vars, string $url): void
    {
        $raiser = User::find($rfi->raised_by);
        if ($raiser && $raiser->id !== auth()->id()) {
            $this->notifications->notifyUser($slug, $raiser, $vars, $url);
        }
    }
}
