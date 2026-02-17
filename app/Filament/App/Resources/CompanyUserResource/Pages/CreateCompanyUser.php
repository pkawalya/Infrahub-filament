<?php

namespace App\Filament\App\Resources\CompanyUserResource\Pages;

use App\Filament\App\Resources\CompanyUserResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCompanyUser extends CreateRecord
{
    protected static string $resource = CompanyUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $company = auth()->user()->company;

        // Enforce user limit
        if ($company && !$company->canAddUser()) {
            Notification::make()
                ->danger()
                ->title('User limit reached')
                ->body("Your plan allows a maximum of {$company->max_users} users. Please upgrade your subscription to add more.")
                ->persistent()
                ->send();

            $this->redirect(route('filament.app.pages.settings.upgrade'));
            $this->halt();
        }

        // Ensure company_id is set
        if (empty($data['company_id'])) {
            $data['company_id'] = $company?->id;
        }

        return $data;
    }
}
