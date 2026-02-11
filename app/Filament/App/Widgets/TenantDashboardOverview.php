<?php

namespace App\Filament\App\Widgets;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\WorkOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantDashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Open Work Orders', WorkOrder::whereNotIn('status', ['completed', 'cancelled'])->count())
                ->description('Pending + In Progress')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('primary')
                ->chart([5, 8, 6, 12, 10, 15]),

            Stat::make('Completed This Month', WorkOrder::where('status', 'completed')
                ->whereMonth('completed_at', now()->month)
                ->count())
                ->description('Work orders completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Active Clients', Client::where('is_active', true)->count())
                ->description('Total active clients')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Revenue This Month', '$' . number_format(
                Invoice::where('status', 'paid')
                    ->whereMonth('issue_date', now()->month)
                    ->sum('total_amount'),
                2
            ))
                ->description('From paid invoices')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([200, 450, 300, 700, 500, 800]),
        ];
    }
}
