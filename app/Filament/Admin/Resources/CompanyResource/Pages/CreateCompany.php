<?php

namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Filament\Admin\Resources\CompanyResource;
use App\Models\User;
use App\Services\InvitationService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

            // Send the invitation email to the company admin
            try {
                $invitationService = app(InvitationService::class);
                $invitation = $invitationService->sendInvitation(
                    $user,
                    plainPassword: $adminPassword,
                    invitedBy: auth()->id(),
                );

                if ($invitation) {
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Invitation sent')
                        ->body("An invitation email has been sent to {$adminEmail}.")
                        ->send();
                } else {
                    Log::warning("InvitationService returned null for {$adminEmail} — template may be missing or email dispatch failed");

                    \Filament\Notifications\Notification::make()
                        ->warning()
                        ->title('User created, but invitation email failed')
                        ->body("The email template 'user-invitation' may not exist. Run: php artisan db:seed --class=EmailTemplateSeeder")
                        ->persistent()
                        ->send();
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to send invitation to company admin {$adminEmail}: " . $e->getMessage());

                \Filament\Notifications\Notification::make()
                    ->warning()
                    ->title('Invitation could not be sent')
                    ->body("The user was created, but the invitation email failed: {$e->getMessage()}")
                    ->send();
            }
        }
    }
}
