<?php

namespace App\Filament\App\Resources\CompanyEmailTemplateResource\Pages;

use App\Filament\App\Resources\CompanyEmailTemplateResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListCompanyEmailTemplates extends ListRecords
{
    protected static string $resource = CompanyEmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
