<?php

namespace App\Filament\App\Resources\SubcontractorResource\Pages;

use App\Filament\App\Resources\SubcontractorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubcontractor extends ViewRecord
{
    protected static string $resource = SubcontractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
