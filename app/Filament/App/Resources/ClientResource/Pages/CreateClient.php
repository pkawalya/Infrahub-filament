<?php

namespace App\Filament\App\Resources\ClientResource\Pages;

use App\Filament\App\Resources\ClientResource;
use App\Models\User;
use App\Services\EmailService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;

        // Remove non-model fields (they're dehydrated(false) but just in case)
        unset($data['create_portal_account'], $data['portal_password']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $formData = $this->form->getRawState();

        if (empty($formData['create_portal_account']) || empty($formData['email'])) {
            return;
        }

        $client = $this->record;
        $password = $formData['portal_password'] ?? \Illuminate\Support\Str::random(10);

        // Check if a user with this email already exists
        $existingUser = User::where('email', $client->email)->first();

        if ($existingUser) {
            // Link existing user to this client
            $client->update(['user_id' => $existingUser->id]);

            Notification::make()
                ->warning()
                ->title('User already exists')
                ->body("A user with email {$client->email} already exists. The client has been linked to that account.")
                ->persistent()
                ->send();

            return;
        }

        try {
            // Create the portal user
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'password' => Hash::make($password),
                'user_type' => 'client',
                'company_id' => $client->company_id,
                'must_change_password' => true,
                'email_verified_at' => now(),
            ]);

            // Link user to client
            $client->update(['user_id' => $user->id]);

            // Send welcome email with credentials
            $companyName = auth()->user()->company?->name ?? config('app.name');
            $loginUrl = config('app.url') . '/client/login';

            try {
                // Send welcome email synchronously (contains credentials — critical)
                app(EmailService::class)->send(
                    'client-welcome',
                    $user,
                    [
                        'client_name' => $client->name,
                        'company_name' => $companyName,
                        'email' => $client->email,
                        'password' => $password,
                        'login_url' => $loginUrl,
                        'user_name' => $client->name,
                    ],
                    $client->company_id,
                    sync: true,
                );

                // Also send invitation email with accept link
                app(\App\Services\InvitationService::class)->sendInvitation(
                    $user,
                    plainPassword: $password,
                    invitedBy: auth()->id(),
                );

                Notification::make()
                    ->success()
                    ->title('Client created with portal access!')
                    ->body("Login credentials sent to {$client->email}")
                    ->send();
            } catch (\Exception $e) {
                Log::warning('Failed to send client welcome email', ['error' => $e->getMessage()]);

                // WARNING: Do NOT embed password in database notification body.
                // Instead, show it briefly as a non-persisted flash notification only.
                // The admin must copy it now — it will NOT be stored in the database.
                Notification::make()
                    ->warning()
                    ->title('Client created — email delivery failed')
                    ->body(
                        "⚠️ Email could not be sent. The portal account was created.\n\n" .
                        "Please securely share the credentials with the client:\n" .
                        "📧 {$client->email} · 🔑 {$password}\n\n" .
                        "This message is NOT saved to the database. Copy it now."
                    )
                    ->persistent()
                    ->sendToDatabase(auth()->user(), isEventDispatched: false) // skip DB storage
                    ->send();
            }
        } catch (\Exception $e) {
            Log::error('Failed to create client user account', ['error' => $e->getMessage()]);

            Notification::make()
                ->danger()
                ->title('Client created but portal account failed')
                ->body('Error: ' . $e->getMessage())
                ->persistent()
                ->send();
        }
    }
}
