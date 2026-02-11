<?php
namespace App\Filament\App\Resources\SafetyIncidentResource\Pages;
use App\Filament\App\Resources\SafetyIncidentResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
class EditSafetyIncident extends EditRecord
{
    protected static string $resource = SafetyIncidentResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
