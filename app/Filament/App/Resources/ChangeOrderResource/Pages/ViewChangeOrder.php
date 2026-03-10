<?php
namespace App\Filament\App\Resources\ChangeOrderResource\Pages;
use App\Filament\App\Resources\ChangeOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewChangeOrder extends ViewRecord
{
    protected static string $resource = ChangeOrderResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
