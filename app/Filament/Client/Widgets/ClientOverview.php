<?php

namespace App\Filament\Client\Widgets;

use App\Models\CdeProject;
use App\Models\Invoice;
use App\Models\PaymentCertificate;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $companyId = $user?->company_id;

        $projects = CdeProject::where('company_id', $companyId)->count();
        $activeProjects = CdeProject::where('company_id', $companyId)->where('status', 'active')->count();

        $pendingInvoices = Invoice::where('company_id', $companyId)->where('status', 'sent')->count();
        $pendingCerts = PaymentCertificate::where('company_id', $companyId)->where('status', 'submitted')->count();

        return [
            Stat::make('Your Projects', $projects)
                ->description("{$activeProjects} active")
                ->icon('heroicon-o-building-office')
                ->color('primary'),
            Stat::make('Pending Invoices', $pendingInvoices)
                ->description('Awaiting payment')
                ->icon('heroicon-o-document-text')
                ->color('warning'),
            Stat::make('Payment Certificates', $pendingCerts)
                ->description('Awaiting certification')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),
        ];
    }
}
