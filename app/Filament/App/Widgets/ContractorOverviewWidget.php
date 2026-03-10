<?php

namespace App\Filament\App\Widgets;

use Filament\Widgets\Widget;
use App\Support\CurrencyHelper;

class ContractorOverviewWidget extends Widget
{
    protected string $view = 'filament.app.widgets.contractor-overview';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function getViewData(): array
    {
        $cid = auth()->user()?->company_id;
        $today = now()->toDateString();

        $data = \DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM equipment_allocations WHERE company_id = ? AND status = 'active') as active_equipment,
                (SELECT COUNT(*) FROM assets WHERE company_id = ? AND status = 'available') as idle_equipment,
                (SELECT COALESCE(SUM(total_cost), 0) FROM equipment_fuel_logs WHERE company_id = ? AND MONTH(log_date) = ? AND YEAR(log_date) = ?) as fuel_cost_month,
                (SELECT COUNT(*) FROM tenders WHERE company_id = ? AND status IN ('identified','preparing') AND deleted_at IS NULL) as pipeline_tenders,
                (SELECT COUNT(*) FROM tenders WHERE company_id = ? AND status = 'submitted' AND deleted_at IS NULL) as submitted_tenders,
                (SELECT COALESCE(SUM(bid_amount), 0) FROM tenders WHERE company_id = ? AND status IN ('identified','preparing','submitted','shortlisted') AND deleted_at IS NULL) as pipeline_value,
                (SELECT COUNT(*) FROM tenders WHERE company_id = ? AND status = 'awarded' AND deleted_at IS NULL) as won_tenders,
                (SELECT COUNT(*) FROM crew_attendance WHERE company_id = ? AND attendance_date = ? AND status = 'present') as present_today,
                (SELECT COUNT(*) FROM crew_attendance WHERE company_id = ? AND attendance_date = ? AND status = 'absent') as absent_today,
                (SELECT COUNT(*) FROM subcontractors WHERE company_id = ? AND status = 'active' AND deleted_at IS NULL AND (insurance_expiry IS NOT NULL AND insurance_expiry < DATE_ADD(?, INTERVAL 30 DAY))) as expiring_subs,
                (SELECT COUNT(*) FROM worker_certifications WHERE company_id = ? AND expiry_date IS NOT NULL AND expiry_date < DATE_ADD(?, INTERVAL 30 DAY)) as expiring_certs
        ", [
            $cid,
            $cid,
            $cid,
            now()->month,
            now()->year,
            $cid,
            $cid,
            $cid,
            $cid,
            $cid,
            $today,
            $cid,
            $today,
            $cid,
            $today,
            $cid,
            $today,
        ]);

        return [
            'equipment' => [
                'active' => (int) $data->active_equipment,
                'idle' => (int) $data->idle_equipment,
                'fuel_cost' => CurrencyHelper::format((float) $data->fuel_cost_month, 0),
            ],
            'tenders' => [
                'pipeline' => (int) $data->pipeline_tenders,
                'submitted' => (int) $data->submitted_tenders,
                'won' => (int) $data->won_tenders,
                'pipeline_value' => CurrencyHelper::format((float) $data->pipeline_value, 0),
            ],
            'crew' => [
                'present' => (int) $data->present_today,
                'absent' => (int) $data->absent_today,
            ],
            'compliance' => [
                'expiring_subs' => (int) $data->expiring_subs,
                'expiring_certs' => (int) $data->expiring_certs,
            ],
        ];
    }
}
