<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\ServerMonitorWidget;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;

class ServerMonitor extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected string $view = 'filament.admin.pages.server-monitor';

    protected static ?string $title = 'Server Monitor';

    protected static ?string $navigationLabel = 'Server Monitor';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 99;

    protected function getHeaderWidgets(): array
    {
        return [
            ServerMonitorWidget::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}
