<?php

namespace App\Filament\App\Widgets;

use App\Models\BidStage;
use App\Models\Tender;
use App\Models\TenderBid;
use App\Models\TenderStage;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenderPipelineWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $companyId = auth()->user()?->company_id;
        if (!$companyId) {
            return [];
        }

        $stats = [];

        // ── Tender Stage Summary ─────────────────────────────
        $tenderStages = TenderStage::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->withCount('tenders')
            ->get();

        if ($tenderStages->isNotEmpty()) {
            foreach ($tenderStages as $stage) {
                $stats[] = Stat::make($stage->name, $stage->tenders_count)
                    ->description('Tenders')
                    ->color($stage->color)
                    ->icon($stage->icon ?? 'heroicon-o-document-text');
            }
        }

        // Add total tenders
        $totalTenders = Tender::where('company_id', $companyId)->count();
        $stats[] = Stat::make('Total Tenders', $totalTenders)
            ->description('All tenders')
            ->color('primary')
            ->icon('heroicon-o-document-magnifying-glass');

        // Add total bids
        $totalBids = TenderBid::where('company_id', $companyId)->count();
        $stats[] = Stat::make('Total Bids', $totalBids)
            ->description('Across all tenders')
            ->color('info')
            ->icon('heroicon-o-document-arrow-up');

        // Overdue tenders
        $overdue = Tender::where('company_id', $companyId)
            ->whereNotIn('status', ['submitted', 'awarded', 'lost', 'withdrawn'])
            ->whereNotNull('submission_deadline')
            ->where('submission_deadline', '<', now())
            ->count();

        if ($overdue > 0) {
            $stats[] = Stat::make('Overdue', $overdue)
                ->description('Past deadline')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle');
        }

        return $stats;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->company_id && in_array($user->user_type, ['super_admin', 'company_admin', 'manager']);
    }
}
