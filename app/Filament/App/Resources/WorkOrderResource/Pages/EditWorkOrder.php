<?php
namespace App\Filament\App\Resources\WorkOrderResource\Pages;
use App\Filament\App\Resources\WorkOrderResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
class EditWorkOrder extends EditRecord
{
    protected static string $resource = WorkOrderResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
