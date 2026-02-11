<?php
namespace App\Filament\App\Resources\SafetyIncidentResource\Pages;
use App\Filament\App\Resources\SafetyIncidentResource;
use Filament\Resources\Pages\ListRecords;
class ListSafetyIncidents extends ListRecords
{
    protected static string $resource = SafetyIncidentResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
