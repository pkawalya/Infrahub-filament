<?php

namespace App\Filament\App\Resources\EquipmentAllocations\Pages;

use App\Filament\App\Resources\EquipmentAllocations\EquipmentAllocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEquipmentAllocations extends ManageRecords
{
    protected static string $resource = EquipmentAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
