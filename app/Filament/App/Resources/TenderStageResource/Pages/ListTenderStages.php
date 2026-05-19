<?php

namespace App\Filament\App\Resources\TenderStageResource\Pages;

use App\Filament\App\Resources\TenderStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTenderStages extends ListRecords
{
    protected static string $resource = TenderStageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
