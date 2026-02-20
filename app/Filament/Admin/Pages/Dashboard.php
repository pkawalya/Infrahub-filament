<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $title = 'Platform Dashboard';

    public function getWidgets(): array
    {
        return [
            Widgets\ServerMonitorWidget::class,
            Widgets\PlatformOverview::class,
            Widgets\CompaniesByPlanChart::class,
            Widgets\UserGrowthChart::class,
            Widgets\RevenueByPlanChart::class,
            Widgets\CompanyStatusChart::class,
            Widgets\RecentCompaniesTable::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 2,
        ];
    }
}
