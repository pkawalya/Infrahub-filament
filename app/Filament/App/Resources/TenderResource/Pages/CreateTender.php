<?php

namespace App\Filament\App\Resources\TenderResource\Pages;

use App\Filament\App\Resources\TenderResource;
use App\Models\TenderStage;
use Filament\Resources\Pages\CreateRecord;

class CreateTender extends CreateRecord
{
    protected static string $resource = TenderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;
        $data['created_by'] = auth()->id();

        // Auto-assign default stage if none selected
        if (empty($data['tender_stage_id'])) {
            $default = TenderStage::getDefault();
            $data['tender_stage_id'] = $default?->id;
        }
        $data['stage_changed_at'] = now();

        return $data;
    }
}
