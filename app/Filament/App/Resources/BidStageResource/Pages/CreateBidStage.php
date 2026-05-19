<?php

namespace App\Filament\App\Resources\BidStageResource\Pages;

use App\Filament\App\Resources\BidStageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBidStage extends CreateRecord
{
    protected static string $resource = BidStageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;
        return $data;
    }
}
