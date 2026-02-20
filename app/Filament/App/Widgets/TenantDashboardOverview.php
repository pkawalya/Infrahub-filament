<?php

namespace App\Filament\App\Widgets;

use App\Models\CdeProject;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\SafetyIncident;
use App\Models\Task;
use App\Models\WorkOrder;
use App\Support\CurrencyHelper;
use Filament\Widgets\Widget;

class TenantDashboardOverview extends Widget
{
    protected string $view = 'filament.app.widgets.tenant-overview';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public function getViewData(): array
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
            'stats' => [
                'projects' => [
                    'active' => $activeProjects,
                    'total' => $totalProjects,
                ],
                'tasks' => [
                    'open' => $openTasks,
                    'overdue' => $overdueTasks,
                ],
                'workOrders' => [
                    'open' => $openWO,
                    'completed' => $completedWO,
                ],
                'revenue' => [
                    'current' => CurrencyHelper::format($monthlyRevenue, 2),
                    'change' => $revenueChange,
                ],
            ],
        ];
    }
}
