<?php
namespace App\Filament\App\Resources\WorkOrderResource\Pages;
use App\Filament\App\Resources\WorkOrderResource;
use Filament\Resources\Pages\CreateRecord;
class CreateWorkOrder extends CreateRecord
{
    protected static string $resource = WorkOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
