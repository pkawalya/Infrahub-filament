<?php

namespace App\Filament\App\Resources\CompanyUserResource\Pages;

use App\Filament\App\Resources\CompanyUserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCompanyUser extends CreateRecord
{
    protected static string $resource = CompanyUserResource::class;

    /**
     * Store the plain-text password before Filament hashes it,
     * so the UserObserver can send it in the welcome email.
     */
    private ?string $capturedPassword = null;

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

        // Capture the plain password before hashing
        $this->capturedPassword = $data['password'] ?? null;

        // Force password change on first login
        $data['must_change_password'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attach the plain password so the Observer can send the welcome email
        if ($this->capturedPassword && $this->record instanceof User) {
            $this->record->plainPassword = $this->capturedPassword;
            // Re-trigger the observer logic manually since `created` already fired
            app(\App\Observers\UserObserver::class)->created($this->record);
        }
    }
}
