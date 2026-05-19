<?php

namespace App\Filament\App\Resources\TenderStageResource\Pages;

use App\Filament\App\Resources\TenderStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTenderStage extends EditRecord
{
    protected static string $resource = TenderStageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
