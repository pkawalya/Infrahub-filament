<?php
namespace App\Filament\App\Resources\AssetResource\Pages;
use App\Filament\App\Resources\AssetResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
