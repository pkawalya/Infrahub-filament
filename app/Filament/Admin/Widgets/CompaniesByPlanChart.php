<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class CompaniesByPlanChart extends ChartWidget
{
    protected ?string $heading = 'Companies by Subscription Plan';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $plans = Subscription::withCount('companies')
            ->orderBy('sort_order')
            ->get();

        $labels = $plans->pluck('name')->toArray();
        $counts = $plans->pluck('companies_count')->toArray();

        // Count companies with no subscription
        $noSub = Company::whereNull('subscription_id')->count();
        if ($noSub > 0) {
            $labels[] = 'No Plan';
            $counts[] = $noSub;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Companies',
                    'data' => $counts,
                    'backgroundColor' => [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(107, 114, 128, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(99, 102, 241)',
                        'rgb(139, 92, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(107, 114, 128)',
                    ],
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                    'grid' => ['color' => 'rgba(107, 114, 128, 0.1)'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
