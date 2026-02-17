<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\ProjectTimelineWidget;
use App\Filament\App\Widgets\TenantDashboardOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            TenantDashboardOverview::class,
            ProjectTimelineWidget::class,
        ];
    }
}
