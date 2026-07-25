<?php
namespace App\Filament\App\Resources\ChangeOrderResource\Pages;
use App\Filament\App\Resources\ChangeOrderResource;
use Filament\Resources\Pages\CreateRecord;
class CreateChangeOrder extends CreateRecord
{
    protected static string $resource = ChangeOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] ??= 'draft';
        return $data;
    }
}
