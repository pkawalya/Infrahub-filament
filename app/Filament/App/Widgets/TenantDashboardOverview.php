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
        $now = now();

        // Single query for project, task, and work order stats
        $stats = \DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM cde_projects WHERE company_id = ? AND deleted_at IS NULL AND status = 'active') as active_projects,
                (SELECT COUNT(*) FROM cde_projects WHERE company_id = ? AND deleted_at IS NULL) as total_projects,
                (SELECT COUNT(*) FROM work_orders WHERE company_id = ? AND status NOT IN ('completed','cancelled')) as open_wo,
                (SELECT COUNT(*) FROM work_orders WHERE company_id = ? AND status = 'completed' AND MONTH(completed_at) = ? AND YEAR(completed_at) = ?) as completed_wo,
                (SELECT COUNT(*) FROM tasks WHERE company_id = ? AND status NOT IN ('done','cancelled')) as open_tasks,
                (SELECT COUNT(*) FROM tasks WHERE company_id = ? AND status NOT IN ('done','cancelled') AND due_date < ?) as overdue_tasks
        ", [$companyId, $companyId, $companyId, $companyId, $now->month, $now->year, $companyId, $companyId, $now]);

        // Revenue query
        $revenue = \DB::selectOne("
            SELECT
                COALESCE(SUM(CASE WHEN MONTH(issue_date) = ? AND YEAR(issue_date) = ? THEN total_amount ELSE 0 END), 0) as current_month,
                COALESCE(SUM(CASE WHEN MONTH(issue_date) = ? AND YEAR(issue_date) = ? THEN total_amount ELSE 0 END), 0) as last_month
            FROM invoices WHERE company_id = ? AND status = 'paid'
              AND issue_date >= ?
        ", [$now->month, $now->year, $now->subMonth()->month, $now->subMonth()->year, $companyId, $now->copy()->subMonths(2)->startOfMonth()]);

        $monthlyRevenue = (float) $revenue->current_month;
        $lastMonthRevenue = (float) $revenue->last_month;

        $revenueChange = $lastMonthRevenue > 0
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : ($monthlyRevenue > 0 ? 100 : 0);

        return [
            'stats' => [
                'projects' => [
                    'active' => (int) $stats->active_projects,
                    'total' => (int) $stats->total_projects,
                ],
                'tasks' => [
                    'open' => (int) $stats->open_tasks,
                    'overdue' => (int) $stats->overdue_tasks,
                ],
                'workOrders' => [
                    'open' => (int) $stats->open_wo,
                    'completed' => (int) $stats->completed_wo,
                ],
                'revenue' => [
                    'current' => CurrencyHelper::format($monthlyRevenue, 2),
                    'change' => $revenueChange,
                ],
            ],
        ];
    }
}
