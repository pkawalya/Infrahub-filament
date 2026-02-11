<?php

namespace App\Filament\App\Resources\CompanyRoleResource\Pages;

use App\Filament\App\Resources\CompanyRoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyRole extends CreateRecord
{
    protected static string $resource = CompanyRoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Always set company_id for company admins
        $user = auth()->user();
        if ($user && $user->isCompanyAdmin() && !$user->isSuperAdmin()) {
            $data['company_id'] = $user->company_id;
        }

        // Default guard
        if (empty($data['guard_name'])) {
            $data['guard_name'] = 'web';
        }

        return $data;
    }
}
