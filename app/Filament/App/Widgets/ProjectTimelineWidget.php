<?php

namespace App\Filament\App\Widgets;

use App\Models\CdeProject;
use App\Models\Milestone;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class ProjectTimelineWidget extends Widget
{
    protected string $view = 'filament.app.widgets.project-timeline';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getViewData(): array
    {
        $companyId = auth()->user()?->company_id;

        $projects = CdeProject::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->orderBy('start_date')
            ->get();

        if ($projects->isEmpty()) {
            return ['projects' => collect(), 'timelineStart' => now(), 'timelineEnd' => now(), 'months' => collect()];
        }

        // Calculate the overall timeline range
        $timelineStart = $projects->min('start_date')->copy()->startOfMonth();
        $timelineEnd = $projects->max('end_date')->copy()->endOfMonth();
        $totalDays = $timelineStart->diffInDays($timelineEnd) ?: 1;

        // Generate month labels
        $months = collect();
        $cursor = $timelineStart->copy();
        while ($cursor->lte($timelineEnd)) {
            $monthStart = $cursor->copy();
            $monthEnd = $cursor->copy()->endOfMonth();
            $monthDays = $monthStart->diffInDays($monthEnd->min($timelineEnd)) + 1;

            $months->push([
                'label' => $cursor->format('M Y'),
                'short' => $cursor->format('M'),
                'width' => round(($monthDays / $totalDays) * 100, 2),
                'isCurrent' => $cursor->isSameMonth(now()),
            ]);
            $cursor->addMonth()->startOfMonth();
        }

        // Build project timeline data
        $projectData = $projects->map(function (CdeProject $project) use ($timelineStart, $totalDays) {
            $start = $project->start_date;
            $end = $project->end_date;

            $leftPercent = round(($timelineStart->diffInDays($start) / $totalDays) * 100, 2);
            $widthPercent = round(($start->diffInDays($end) / $totalDays) * 100, 2);

            // Calculate progress (elapsed time vs total)
            $elapsed = $start->diffInDays(now());
            $totalProjectDays = $start->diffInDays($end) ?: 1;
            $progress = $start->isFuture() ? 0 : min(100, round(($elapsed / $totalProjectDays) * 100));

            // Get milestones for this project
            $milestones = Milestone::where('cde_project_id', $project->id)
                ->orderBy('target_date')
                ->get()
                ->map(function (Milestone $m) use ($timelineStart, $totalDays) {
                    $pos = round(($timelineStart->diffInDays($m->target_date) / $totalDays) * 100, 2);
                    return [
                        'name' => $m->name,
                        'date' => $m->target_date->format('M d, Y'),
                        'status' => $m->status,
                        'priority' => $m->priority,
                        'position' => $pos,
                        'isOverdue' => $m->status !== 'completed' && $m->target_date->isPast(),
                    ];
                });

            $statusColors = [
                'planning' => '#6366f1',   // indigo
                'active' => '#10b981',     // emerald
                'on_hold' => '#f59e0b',    // amber
                'completed' => '#6b7280',  // gray
                'cancelled' => '#ef4444',  // red
            ];

            return [
                'id' => $project->id,
                'name' => $project->name,
                'code' => $project->code,
                'status' => $project->status,
                'statusLabel' => CdeProject::$statuses[$project->status] ?? $project->status,
                'startDate' => $start->format('M d, Y'),
                'endDate' => $end->format('M d, Y'),
                'leftPercent' => max(0, $leftPercent),
                'widthPercent' => max(1, $widthPercent),
                'progress' => $progress,
                'color' => $statusColors[$project->status] ?? '#6366f1',
                'milestones' => $milestones,
                'url' => route('filament.app.resources.cde-projects.view', $project),
                'budget' => $project->budget ? '$' . number_format($project->budget, 0) : null,
            ];
        });

        // Today marker position
        $todayPercent = null;
        if (now()->between($timelineStart, $timelineEnd)) {
            $todayPercent = round(($timelineStart->diffInDays(now()) / $totalDays) * 100, 2);
        }

        return [
            'projects' => $projectData,
            'months' => $months,
            'todayPercent' => $todayPercent,
        ];
    }
}
