<?php

namespace App\Filament\App\Resources\EquipmentFuelLogs\Pages;

use App\Filament\App\Resources\EquipmentFuelLogs\EquipmentFuelLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEquipmentFuelLogs extends ManageRecords
{
    protected static string $resource = EquipmentFuelLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver(false)
                ->modalWidth('5xl'),
        ];
    }
}
