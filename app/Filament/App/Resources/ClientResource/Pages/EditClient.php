<?php
namespace App\Filament\App\Resources\ClientResource\Pages;
use App\Filament\App\Resources\ClientResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
