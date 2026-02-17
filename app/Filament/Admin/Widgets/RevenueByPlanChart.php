<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class RevenueByPlanChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Breakdown by Plan';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 1;
    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $plans = Subscription::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $labels = [];
        $revenue = [];
        $colors = [
            'rgba(99, 102, 241, 0.85)',
            'rgba(16, 185, 129, 0.85)',
            'rgba(245, 158, 11, 0.85)',
            'rgba(139, 92, 246, 0.85)',
            'rgba(236, 72, 153, 0.85)',
            'rgba(14, 165, 233, 0.85)',
        ];

        foreach ($plans as $i => $plan) {
            $activeCount = Company::where('subscription_id', $plan->id)
                ->where('is_active', true)
                ->count();
            $planRevenue = $activeCount * $plan->monthly_price;

            if ($planRevenue > 0 || $activeCount > 0) {
                $labels[] = $plan->name;
                $revenue[] = round($planRevenue, 2);
            }
        }

        if (empty($revenue)) {
            $labels = ['No Revenue Data'];
            $revenue = [0];
        }

        return [
            'datasets' => [
                [
                    'data' => $revenue,
                    'backgroundColor' => array_slice($colors, 0, count($labels)),
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => ['padding' => 16],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
