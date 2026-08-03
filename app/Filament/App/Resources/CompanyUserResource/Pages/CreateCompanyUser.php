<?php

namespace App\Filament\App\Resources\CompanyUserResource\Pages;

use App\Filament\App\Resources\CompanyUserResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCompanyUser extends CreateRecord
{
    protected static string $resource = CompanyUserResource::class;

    /**
     * Store the plain-text password before Filament hashes it,
     * so the UserObserver can send it in the welcome email.
     */
    private ?string $capturedPassword = null;
    private bool $isExistingUserAttached = false;

    protected function handleRecordCreation(array $data): Model
    {
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            $company = auth()->user()->company;
            $companyId = $data['company_id'] ?? $company?->id;

            if ($companyId) {
                $existingUser->attachToCompany($companyId, [
                    'user_type' => $data['user_type'] ?? 'member',
                    'job_title' => $data['job_title'] ?? $existingUser->job_title,
                    'department' => $data['department'] ?? $existingUser->department,
                    'phone' => $data['phone'] ?? $existingUser->phone,
                    'is_active' => $data['is_active'] ?? true,
                ]);
            }

            if (!empty($data['roles'])) {
                $existingUser->assignRole($data['roles']);
            }

            $this->isExistingUserAttached = true;

            Notification::make()
                ->success()
                ->title('User Added to Company')
                ->body("Account {$existingUser->email} already existed and has been added to your company.")
                ->send();

            return $existingUser;
        }

        return parent::handleRecordCreation($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $company = auth()->user()->company;

        // Enforce user limit
        if ($company && !$company->canAddUser()) {
            $effectiveMax = $company->getEffectiveMaxUsers();
            Notification::make()
                ->danger()
                ->title('User limit reached')
                ->body("Your plan allows a maximum of {$effectiveMax} users (including addons). You currently have {$company->getCachedUserCount()}. Please upgrade your plan or add extra users.")
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
        $this->capturedPassword = $this->data['password'] ?? null;

        // Force password change on first login
        $data['must_change_password'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attach the plain password so the Observer can send the welcome email
        if ($this->capturedPassword && $this->record instanceof User && !$this->isExistingUserAttached) {
            $this->record->plainPassword = $this->capturedPassword;
            // Re-trigger the observer logic manually since `created` already fired
            app(\App\Observers\UserObserver::class)->created($this->record);
        }
    }
}
