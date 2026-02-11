<?php

namespace App\Filament\App\Resources\CompanyRoleResource\Pages;

use App\Filament\App\Resources\CompanyRoleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListCompanyRoles extends ListRecords
{
    protected static string $resource = CompanyRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
