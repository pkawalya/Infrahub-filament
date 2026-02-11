<?php

namespace App\Filament\App\Resources\CompanyRoleResource\Pages;

use App\Filament\App\Resources\CompanyRoleResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditCompanyRole extends EditRecord
{
    protected static string $resource = CompanyRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
