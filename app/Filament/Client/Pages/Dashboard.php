<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Client Dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Client\Widgets\ClientOverview::class,
        ];
    }
}
