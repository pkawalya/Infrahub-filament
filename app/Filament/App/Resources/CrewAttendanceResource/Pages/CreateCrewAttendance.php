<?php

namespace App\Filament\App\Resources\CrewAttendanceResource\Pages;

use App\Filament\App\Resources\CrewAttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrewAttendance extends CreateRecord
{
    protected static string $resource = CrewAttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;
        return $data;
    }
}
