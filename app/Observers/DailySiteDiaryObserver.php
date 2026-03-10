<?php

namespace App\Observers;

use App\Models\DailySiteDiary;
use App\Services\ModuleNotificationService;

class DailySiteDiaryObserver
{
    public function __construct(protected ModuleNotificationService $notifications)
    {
    }

    public function updated(DailySiteDiary $diary): void
    {
        // Notify when diary is submitted for approval
        if ($diary->isDirty('status') && $diary->status === 'submitted') {
            $this->notifications->notifyCompanyAdmins('site-diary-submitted', $diary->company_id, [
                'diary_date' => $diary->diary_date?->format('M d, Y') ?? '',
                'project_name' => $diary->project?->name ?? '',
                'submitted_by' => auth()->user()?->name ?? 'System',
                'weather' => $diary->weather ?? '',
                'total_workers' => $diary->total_workers ?? 0,
            ], url("/app/daily-site-diaries/{$diary->id}"));
        }

        // Notify author when diary is approved
        if ($diary->isDirty('status') && $diary->status === 'approved') {
            $author = $diary->created_by ? \App\Models\User::find($diary->created_by) : null;
            if ($author && $author->id !== auth()->id()) {
                $this->notifications->notifyUser('site-diary-approved', $author, [
                    'diary_date' => $diary->diary_date?->format('M d, Y') ?? '',
                    'project_name' => $diary->project?->name ?? '',
                    'approved_by' => auth()->user()?->name ?? 'System',
                ], url("/app/daily-site-diaries/{$diary->id}"));
            }
        }
    }
}
