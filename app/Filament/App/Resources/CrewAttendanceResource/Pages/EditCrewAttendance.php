<?php

namespace App\Filament\App\Resources\CrewAttendanceResource\Pages;

use App\Filament\App\Resources\CrewAttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrewAttendance extends EditRecord
{
    protected static string $resource = CrewAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
