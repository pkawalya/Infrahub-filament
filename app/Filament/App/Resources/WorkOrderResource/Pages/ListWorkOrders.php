<?php
namespace App\Filament\App\Resources\WorkOrderResource\Pages;
use App\Filament\App\Resources\WorkOrderResource;
use Filament\Resources\Pages\ListRecords;
class ListWorkOrders extends ListRecords
{
    protected static string $resource = WorkOrderResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
