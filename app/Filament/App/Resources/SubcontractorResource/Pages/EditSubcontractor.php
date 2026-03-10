<?php

namespace App\Filament\App\Resources\SubcontractorResource\Pages;

use App\Filament\App\Resources\SubcontractorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubcontractor extends EditRecord
{
    protected static string $resource = SubcontractorResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
