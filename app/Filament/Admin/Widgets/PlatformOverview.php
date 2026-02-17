<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\CdeProject;
use App\Models\Module;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $trialCompanies = Company::where('is_trial', true)->count();
        $totalUsers = User::count();
        $totalProjects = CdeProject::count();
        $activePlans = Subscription::where('is_active', true)->count();
        $activeModules = Module::where('is_active', true)->count();

        // Calculate MRR
        $mrr = Company::join('subscriptions', 'companies.subscription_id', '=', 'subscriptions.id')
            ->where('companies.is_active', true)
            ->sum('subscriptions.monthly_price');

        // Monthly registration trend (last 6 months)
        $companyTrend = collect();
        $userTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $companyTrend->push(
                Company::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
            $userTrend->push(
                User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            );
        }

        return [
            Stat::make('Total Companies', $totalCompanies)
                ->description($activeCompanies . ' active, ' . ($totalCompanies - $activeCompanies) . ' pending')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart($companyTrend->toArray()),

            Stat::make('Total Users', $totalUsers)
                ->description('Across ' . $activeCompanies . ' companies')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart($userTrend->toArray()),

            Stat::make('Monthly Revenue', '$' . number_format($mrr, 2))
                ->description('MRR from active subscriptions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([100, 250, 400, 500, 650, (int) $mrr]),

            Stat::make('Total Projects', $totalProjects)
                ->description(CdeProject::where('status', 'active')->count() . ' active projects')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('info')
                ->chart([2, 4, 5, 6, 8, $totalProjects]),

            Stat::make('Trial Companies', $trialCompanies)
                ->description($trialCompanies > 0 ? 'Potential conversions' : 'None currently')
                ->descriptionIcon('heroicon-m-clock')
                ->color($trialCompanies > 0 ? 'danger' : 'gray'),

            Stat::make('Active Modules', $activeModules . ' / ' . Module::count())
                ->description($activePlans . ' subscription plans')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('success'),
        ];
    }
}
