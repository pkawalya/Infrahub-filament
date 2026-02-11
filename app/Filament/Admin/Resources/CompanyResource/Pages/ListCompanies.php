<?php
namespace App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Filament\Admin\Resources\CompanyResource;
use Filament\Resources\Pages\ListRecords;
class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;
    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
