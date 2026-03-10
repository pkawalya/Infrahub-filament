<?php
namespace App\Filament\App\Resources\DailySiteDiaryResource\Pages;
use App\Filament\App\Resources\DailySiteDiaryResource;
use Filament\Resources\Pages\CreateRecord;
class CreateDailySiteDiary extends CreateRecord
{
    protected static string $resource = DailySiteDiaryResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;
        $data['prepared_by'] = auth()->id();
        return $data;
    }
}
