<?php
namespace App\Filament\App\Resources\TaskResource\Pages;
use App\Filament\App\Resources\TaskResource;
use Filament\Resources\Pages\CreateRecord;
class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
