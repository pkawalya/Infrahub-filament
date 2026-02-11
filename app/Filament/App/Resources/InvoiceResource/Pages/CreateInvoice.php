<?php
namespace App\Filament\App\Resources\InvoiceResource\Pages;
use App\Filament\App\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
