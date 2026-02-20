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
    protected int|string|array $columnSpan = 'full';

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

        // Build sparkline trends (last 6 months)
        $projectTrend = collect();
        $taskTrend = collect();
        $woTrend = collect();
        $revenueTrend = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $projectTrend->push(
                CdeProject::where('company_id', $companyId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
            $taskTrend->push(
                Task::where('company_id', $companyId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
            $woTrend->push(
                WorkOrder::where('company_id', $companyId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
            $revenueTrend->push(
                (int) Invoice::where('company_id', $companyId)
                    ->where('status', 'paid')
                    ->whereYear('issue_date', $date->year)
                    ->whereMonth('issue_date', $date->month)
                    ->sum('total_amount')
            );
        }

        $monthlyRevenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereMonth('issue_date', now()->month)
            ->sum('total_amount');

        $lastMonthRevenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereMonth('issue_date', now()->subMonth()->month)
            ->whereYear('issue_date', now()->subMonth()->year)
            ->sum('total_amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : ($monthlyRevenue > 0 ? 100 : 0);

        return [
            Stat::make('Active Projects', $activeProjects)
                ->description($totalProjects . ' total · ' . ($totalProjects - $activeProjects) . ' other')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart($projectTrend->toArray()),

            Stat::make('Open Tasks', $openTasks)
                ->description(
                    $overdueTasks > 0
                    ? $overdueTasks . ' overdue — action needed'
                    : '✓ All tasks on track'
                )
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'success')
                ->chart($taskTrend->toArray()),

            Stat::make('Work Orders', $openWO . ' open')
                ->description($completedWO . ' completed this month')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info')
                ->chart($woTrend->toArray()),

            Stat::make('Revenue This Month', CurrencyHelper::format($monthlyRevenue, 2))
                ->description(
                    $revenueChange >= 0
                    ? '↑ ' . $revenueChange . '% from last month'
                    : '↓ ' . abs($revenueChange) . '% from last month'
                )
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($revenueTrend->toArray()),
        ];
    }
}
