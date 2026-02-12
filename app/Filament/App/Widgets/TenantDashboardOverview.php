<?php

namespace App\Filament\App\Widgets;

use App\Models\CdeProject;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\SafetyIncident;
use App\Models\Task;
use App\Models\WorkOrder;
use App\Support\CurrencyHelper;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantDashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $companyId = auth()->user()?->company_id;

        $activeProjects = CdeProject::where('company_id', $companyId)->where('status', 'active')->count();
        $totalProjects = CdeProject::where('company_id', $companyId)->count();

        $openWO = WorkOrder::where('company_id', $companyId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $completedWO = WorkOrder::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)
            ->count();

        $openTasks = Task::where('company_id', $companyId)
            ->whereNotIn('status', ['done', 'cancelled'])
            ->count();
        $overdueTasks = Task::where('company_id', $companyId)
            ->whereNotIn('status', ['done', 'cancelled'])
            ->where('due_date', '<', now())
            ->count();

        return [
            Stat::make('Active Projects', $activeProjects)
                ->description($totalProjects . ' total projects')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart([3, 5, 4, 6, 5, 7, $activeProjects]),

            Stat::make('Open Tasks', $openTasks)
                ->description($overdueTasks > 0 ? $overdueTasks . ' overdue' : 'All on track')
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'success'),

            Stat::make('Work Orders', $openWO)
                ->description($completedWO . ' completed this month')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info')
                ->chart([5, 8, 6, 12, 10, 15, $openWO]),

            Stat::make('Revenue This Month', CurrencyHelper::format(
                Invoice::where('company_id', $companyId)
                    ->where('status', 'paid')
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
