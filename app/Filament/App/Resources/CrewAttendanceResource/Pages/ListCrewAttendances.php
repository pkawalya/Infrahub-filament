<?php

namespace App\Filament\App\Resources\CrewAttendanceResource\Pages;

use App\Filament\App\Resources\CrewAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCrewAttendances extends ListRecords
{
    protected static string $resource = CrewAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
