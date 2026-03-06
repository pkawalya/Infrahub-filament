<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?string $capturedPassword = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Capture plain password before hashing
        $this->capturedPassword = $data['password'] ?? null;

        // Force password change on first login
        $data['must_change_password'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->capturedPassword && $this->record instanceof User) {
            $this->record->plainPassword = $this->capturedPassword;
            app(\App\Observers\UserObserver::class)->created($this->record);
        }
    }
}
