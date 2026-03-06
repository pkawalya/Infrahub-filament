<?php

namespace App\Filament\App\Widgets;

use App\Models\BillingRecord;
use App\Models\CdeProject;
use App\Models\Invoice;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class BillingOverviewWidget extends Widget
{
    protected string $view = 'filament.app.widgets.billing-overview';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 99;

    public function getData(): array
    {
        $user = auth()->user();
        $companyId = $user?->company_id;

        if (!$companyId) {
            return [
                'activeProjects' => 0,
                'currentBill' => null,
                'overdueInvoices' => 0,
                'overdueAmount' => 0,
                'unpaidBills' => 0,
                'unpaidBillsAmount' => 0,
            ];
        }

        $activeProjects = CdeProject::where('company_id', $companyId)
            ->where('billing_status', 'active')
            ->count();

        // Current month billing
        $currentPeriod = now()->format('Y-m');
        $currentBill = BillingRecord::where('company_id', $companyId)
            ->where('period', $currentPeriod)
            ->first();

        // Overdue invoices
        $overdueInvoices = Invoice::where('company_id', $companyId)
            ->where('status', 'overdue')
            ->get();

        // Unpaid platform bills
        $unpaidBills = BillingRecord::where('company_id', $companyId)
            ->unpaid()
            ->get();

        return [
            'activeProjects' => $activeProjects,
            'currentBill' => $currentBill,
            'overdueInvoices' => $overdueInvoices->count(),
            'overdueAmount' => $overdueInvoices->sum('balance_due'),
            'unpaidBills' => $unpaidBills->count(),
            'unpaidBillsAmount' => $unpaidBills->sum('total_amount'),
        ];
    }
}
