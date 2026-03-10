<?php

namespace App\Filament\App\Resources\TenderResource\Pages;

use App\Filament\App\Resources\TenderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTender extends CreateRecord
{
    protected static string $resource = TenderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;
        $data['created_by'] = auth()->id();
        return $data;
    }
}
