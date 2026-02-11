<?php
namespace App\Filament\App\Resources\CdeProjectResource\Pages;
use App\Filament\App\Resources\CdeProjectResource;
use Filament\Resources\Pages\ListRecords;
class ListCdeProjects extends ListRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
