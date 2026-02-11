<?php
namespace App\Filament\App\Resources\InvoiceResource\Pages;
use App\Filament\App\Resources\InvoiceResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
