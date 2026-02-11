<?php

namespace App\Filament\App\Widgets;

use App\Models\WorkOrder;
use Filament\Widgets\ChartWidget;

class WorkOrdersByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Work Orders by Status';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statuses = WorkOrder::$statuses;
        $counts = [];
        $labels = [];

        foreach ($statuses as $key => $label) {
            $labels[] = $label;
            $counts[] = WorkOrder::where('status', $key)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Work Orders',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#94a3b8',
                        '#818cf8',
                        '#38bdf8',
                        '#fbbf24',
                        '#34d399',
                        '#f87171',
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
