<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Companies', Company::count())
                ->description('Active: ' . Company::where('is_active', true)->count())
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart([7, 12, 14, 18, 15, 22, 25]),

            Stat::make('Total Users', User::count())
                ->description('Across all companies')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([15, 18, 22, 25, 28, 30, 35]),

            Stat::make('Active Plans', Subscription::where('is_active', true)->count())
                ->description('Subscription Plans')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('Revenue (MRR)', '$' . number_format(
                Company::join('subscriptions', 'companies.subscription_id', '=', 'subscriptions.id')
                    ->where('companies.is_active', true)
                    ->sum('subscriptions.monthly_price'),
                2
            ))
                ->description('Monthly Recurring Revenue')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning')
                ->chart([100, 250, 400, 500, 650, 800]),
        ];
    }
}
