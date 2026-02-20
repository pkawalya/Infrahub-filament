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
                // 1. Hero KPIs — the executive summary
            Widgets\PlatformOverview::class,

                // 2. Growth & Acquisition charts (side by side)
            Widgets\UserGrowthChart::class,
            Widgets\CompaniesByPlanChart::class,

                // 3. Revenue & Status breakdown (side by side)
            Widgets\RevenueByPlanChart::class,
            Widgets\CompanyStatusChart::class,

                // 4. Recent activity table
            Widgets\RecentCompaniesTable::class,

                // 5. Infrastructure health (also on dedicated page)
            Widgets\ServerMonitorWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 2,
            'xl' => 2,
        ];
    }
}
