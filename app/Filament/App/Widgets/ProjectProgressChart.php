<?php

namespace App\Filament\App\Widgets;

use App\Models\CdeProject;
use Filament\Widgets\ChartWidget;

class ProjectProgressChart extends ChartWidget
{
    protected ?string $heading = 'Project Status Overview';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $user = auth()->user();
        $companyId = $user?->company_id;

        $statuses = CdeProject::$statuses;
        $counts = [];
        $labels = [];

        foreach ($statuses as $key => $label) {
            $count = CdeProject::where('company_id', $companyId)
                ->where('status', $key)
                ->count();
            if ($count > 0 || in_array($key, ['active', 'planning', 'completed'])) {
                $labels[] = $label;
                $counts[] = $count;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#3b82f6', // planning - blue
                        '#10b981', // active - green
                        '#f59e0b', // on_hold - amber
                        '#6b7280', // completed - gray
                        '#ef4444', // cancelled - red
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
