<?php

namespace App\Filament\App\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TasksTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tasks Completed (Last 8 Weeks)';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = auth()->user();
        $companyId = $user?->company_id;

        $weeks = collect();
        $created = collect();
        $completed = collect();

        for ($i = 7; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();
            $label = $start->format('M d');

            $weeks->push($label);
            $created->push(
                Task::where('company_id', $companyId)
                    ->whereBetween('created_at', [$start, $end])
                    ->count()
            );
            $completed->push(
                Task::where('company_id', $companyId)
                    ->where('status', 'done')
                    ->whereBetween('updated_at', [$start, $end])
                    ->count()
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Created',
                    'data' => $created->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Completed',
                    'data' => $completed->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $weeks->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
