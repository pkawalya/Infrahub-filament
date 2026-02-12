<?php

namespace App\Filament\App\Resources\CompanyEmailTemplateResource\Pages;

use App\Filament\App\Resources\CompanyEmailTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyEmailTemplate extends CreateRecord
{
    protected static string $resource = CompanyEmailTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        $data['company_id'] = $user->company_id;
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        return $data;
    }
}
