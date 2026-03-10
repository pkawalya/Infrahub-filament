<?php
namespace App\Filament\App\Resources\ChangeOrderResource\Pages;
use App\Filament\App\Resources\ChangeOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditChangeOrder extends EditRecord
{
    protected static string $resource = ChangeOrderResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
