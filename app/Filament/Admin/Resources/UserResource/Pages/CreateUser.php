<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?string $capturedPassword = null;
    private bool $isExistingUserAttached = false;

    protected function handleRecordCreation(array $data): Model
    {
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            $companyId = $data['company_id'] ?? null;

            if ($companyId) {
                $existingUser->attachToCompany($companyId, [
                    'user_type' => $data['user_type'] ?? $existingUser->user_type,
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

            $companyName = $companyId ? (\App\Models\Company::find($companyId)?->name ?? 'selected company') : 'company';

            Notification::make()
                ->success()
                ->title('User Attached to Company')
                ->body("Account {$existingUser->email} already existed and has been attached to {$companyName}.")
                ->send();

            return $existingUser;
        }

        return parent::handleRecordCreation($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Capture plain password before hashing
        $this->capturedPassword = $this->data['password'] ?? null;

        // Force password change on first login
        $data['must_change_password'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->capturedPassword && $this->record instanceof User && !$this->isExistingUserAttached) {
            $this->record->plainPassword = $this->capturedPassword;
            app(\App\Observers\UserObserver::class)->created($this->record);
        }
    }
}
