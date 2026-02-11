<?php
namespace App\Filament\App\Resources\TaskResource\Pages;
use App\Filament\App\Resources\TaskResource;
use Filament\Resources\Pages\ListRecords;
class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
