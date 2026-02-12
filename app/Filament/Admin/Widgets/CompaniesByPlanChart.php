<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class CompaniesByPlanChart extends ChartWidget
{
    protected ?string $heading = 'Companies by Subscription Plan';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $plans = Subscription::withCount('companies')
            ->orderBy('sort_order')
            ->get();

        $labels = $plans->pluck('name')->toArray();
        $counts = $plans->pluck('companies_count')->toArray();

        // Also count companies with no subscription
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
                        '#3b82f6',
                        '#8b5cf6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#6b7280',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
