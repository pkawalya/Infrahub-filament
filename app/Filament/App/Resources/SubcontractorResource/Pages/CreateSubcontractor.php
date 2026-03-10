<?php

namespace App\Filament\App\Resources\SubcontractorResource\Pages;

use App\Filament\App\Resources\SubcontractorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubcontractor extends CreateRecord
{
    protected static string $resource = SubcontractorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;
        $data['created_by'] = auth()->id();
        return $data;
    }
}
