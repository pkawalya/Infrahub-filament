<?php

namespace App\Filament\App\Resources\CompanyUserResource\Pages;

use App\Filament\App\Resources\CompanyUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyUser extends CreateRecord
{
    protected static string $resource = CompanyUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure company_id is set for company admins
        if (empty($data['company_id'])) {
            $data['company_id'] = auth()->user()?->company_id;
        }

        return $data;
    }
}
