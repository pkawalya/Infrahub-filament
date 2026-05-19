<?php

namespace App\Filament\App\Resources\TenderStageResource\Pages;

use App\Filament\App\Resources\TenderStageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenderStage extends CreateRecord
{
    protected static string $resource = TenderStageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;
        return $data;
    }
}
