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

    public function getHeading(): string
    {
        $company = auth()->user()?->company;
        $settings = $company?->settings['client_portal'] ?? [];
        $welcomeMessage = $settings['welcome_message'] ?? null;

        return $welcomeMessage ? 'Welcome' : 'Client Dashboard';
    }

    public function getSubheading(): ?string
    {
        $company = auth()->user()?->company;
        $settings = $company?->settings['client_portal'] ?? [];

        return $settings['welcome_message'] ?? null;
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 4;
    }
}
