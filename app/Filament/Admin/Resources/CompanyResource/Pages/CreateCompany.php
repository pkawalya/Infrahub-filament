<?php

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Filament\Admin\Resources\CompanyResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove admin fields and module fields before creating company
        unset(
            $data['admin_name'],
            $data['admin_email'],
            $data['admin_password'],
            $data['admin_phone'],
            $data['enabled_modules'],
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        // Enable selected modules
        $modules = $this->data['enabled_modules'] ?? [];
        foreach ($modules as $code) {
            $this->record->enableModule($code, auth()->id());
        }

        // Create the company admin user
        $adminName = $this->data['admin_name'] ?? null;
        $adminEmail = $this->data['admin_email'] ?? null;
        $adminPassword = $this->data['admin_password'] ?? null;

        if ($adminName && $adminEmail && $adminPassword) {
            $user = User::create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'phone' => $this->data['admin_phone'] ?? null,
                'company_id' => $this->record->id,
                'user_type' => 'company_admin',
                'is_active' => true,
            ]);

            // Assign panel_user role so they can access the App panel
            $user->assignRole('panel_user');
        }
    }
}
