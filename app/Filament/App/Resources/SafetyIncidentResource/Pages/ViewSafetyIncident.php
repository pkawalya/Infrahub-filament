<?php

namespace App\Filament\App\Resources\SafetyIncidentResource\Pages;

use App\Filament\App\Resources\SafetyIncidentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSafetyIncident extends ViewRecord
{
    protected static string $resource = SafetyIncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
