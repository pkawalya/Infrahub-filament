<?php

namespace App\Filament\App\Widgets;

use App\Models\CdeDocument;
use App\Models\DocumentSubmission;
use App\Models\Ncr;
use App\Models\SafetyInspection;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WorkflowBottlenecksWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '5m';
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $companyId = auth()->user()?->company_id;
        if (!$companyId) {
            return [];
        }

        $stats = [];

        // ── Documents stuck in under_review > 7 days ──
        $stuckDocs = CdeDocument::where('company_id', $companyId)
            ->where('status', 'under_review')
            ->where('updated_at', '<', now()->subDays(7))
            ->count();

        if ($stuckDocs > 0) {
            $stats[] = Stat::make('Documents Stuck in Review', $stuckDocs)
                ->description('Under review for > 7 days')
                ->color('warning')
                ->icon('heroicon-o-document-text');
        }

        // ── NCRs aging by status bucket ──
        $agingNcrs = Ncr::where('company_id', $companyId)
            ->whereIn('status', ['open', 'investigating', 'corrective_action'])
            ->where('created_at', '<', now()->subDays(14))
            ->count();

        if ($agingNcrs > 0) {
            $stats[] = Stat::make('Aging NCRs', $agingNcrs)
                ->description('Open > 14 days without closure')
                ->color('danger')
                ->icon('heroicon-o-document-text');
        }

        // ── Overdue document submissions ──
        $overdueSubmissions = DocumentSubmission::where('company_id', $companyId)
            ->where('due_date', '<', now())
            ->whereIn('status', ['pending', 'submitted'])
            ->count();

        if ($overdueSubmissions > 0) {
            $stats[] = Stat::make('Overdue Submissions', $overdueSubmissions)
                ->description('Past due date, not approved/waived')
                ->color('danger')
                ->icon('heroicon-o-paper-airplane');
        }

        // ── Overdue inspections ──
        $overdueInspections = SafetyInspection::where('company_id', $companyId)
            ->where('scheduled_date', '<', now())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->count();

        if ($overdueInspections > 0) {
            $stats[] = Stat::make('Overdue Inspections', $overdueInspections)
                ->description('Past scheduled date')
                ->color('warning')
                ->icon('heroicon-o-clipboard-document-check');
        }



        return $stats;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->company_id && in_array($user->user_type, ['super_admin', 'company_admin', 'manager']);
    }
}
