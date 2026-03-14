<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

class OfflineForms extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud-arrow-down';
    protected string $view = 'filament.app.pages.offline-forms';
    protected static string|\UnitEnum|null $navigationGroup = 'Dashboard';
    protected static ?string $navigationLabel = 'Offline Forms';
    protected static ?int $navigationSort = 99;
    protected static ?string $title = 'Offline Data Collection';
    protected static ?string $slug = 'offline-forms';

    public function getSubheading(): ?string
    {
        return 'Capture site diary, attendance, and safety data — even without internet.';
    }
}
