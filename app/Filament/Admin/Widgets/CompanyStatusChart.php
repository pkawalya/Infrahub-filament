<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use Filament\Widgets\ChartWidget;

class CompanyStatusChart extends ChartWidget
{
    protected ?string $heading = 'Company Status Distribution';
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $active = Company::where('is_active', true)->where('is_trial', false)->count();
        $trial = Company::where('is_trial', true)->count();
        $pending = Company::where('is_active', false)->where('is_trial', false)->count();

        return [
            'datasets' => [
                [
                    'data' => [$active, $trial, $pending],
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(14, 165, 233, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => ['Active', 'Trial', 'Pending'],
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
