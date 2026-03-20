<?php

namespace App\Filament\Client\Widgets;

use App\Models\CdeProject;
use App\Models\Client;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class ClientOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $company = $user?->company;

        // Get the client portal settings from the company
        $settings = $company?->settings['client_portal'] ?? [];
        $showInvoices = $settings['show_invoices'] ?? true;
        $showDocuments = $settings['show_documents'] ?? true;
        $welcomeMessage = $settings['welcome_message'] ?? null;

        // Count projects accessible to this client
        $projectQuery = CdeProject::where(function (Builder $q) use ($user) {
            $q->whereHas('client', fn(Builder $c) => $c->where('user_id', $user?->id))
                ->orWhereHas('members', fn(Builder $m) => $m->where('users.id', $user?->id));
        });

        $projects = (clone $projectQuery)->count();
        $activeProjects = (clone $projectQuery)->where('status', 'active')->count();

        $stats = [
            Stat::make('Your Projects', $projects)
                ->description("{$activeProjects} active")
                ->icon('heroicon-o-building-office')
                ->color('primary'),
        ];

        if ($showInvoices) {
            $projectIds = (clone $projectQuery)->pluck('id');

            $pendingInvoices = Invoice::whereIn('cde_project_id', $projectIds)
                ->whereIn('status', ['sent', 'overdue'])
                ->count();

            $totalOwed = Invoice::whereIn('cde_project_id', $projectIds)
                ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
                ->selectRaw('COALESCE(SUM(total_amount) - SUM(amount_paid), 0) as balance')
                ->value('balance') ?? 0;

            $stats[] = Stat::make('Pending Invoices', $pendingInvoices)
                ->description('Awaiting payment')
                ->icon('heroicon-o-document-text')
                ->color($pendingInvoices > 0 ? 'warning' : 'success');

            $stats[] = Stat::make('Outstanding Balance', number_format($totalOwed, 0))
                ->description($company?->currency_symbol ?? '$')
                ->icon('heroicon-o-banknotes')
                ->color($totalOwed > 0 ? 'danger' : 'success');
        }

        if ($showDocuments) {
            $projectIds = $projectIds ?? (clone $projectQuery)->pluck('id');

            $approvedDocs = \App\Models\CdeDocument::whereIn('cde_project_id', $projectIds)
                ->where('status', 'approved')
                ->count();

            $stats[] = Stat::make('Documents Available', $approvedDocs)
                ->description('Approved for review')
                ->icon('heroicon-o-document-check')
                ->color('info');
        }

        return $stats;
    }
}
