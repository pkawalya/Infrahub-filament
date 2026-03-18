<?php

namespace App\Filament\App\Resources\ClientResource\Pages;

use App\Filament\App\Resources\ClientResource;
use App\Models\User;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        $client = $this->record;
        $hasPortalAccess = $client->user_id && User::where('id', $client->user_id)->exists();

        return [
            // Grant Portal Access (only if client doesn't have an account yet)
            Actions\Action::make('grantPortalAccess')
                ->label('Grant Portal Access')
                ->icon('heroicon-o-key')
                ->color('success')
                ->visible(!$hasPortalAccess && !empty($client->email))
                ->requiresConfirmation()
                ->modalHeading('Create Client Portal Account')
                ->modalDescription("This will create a login account for {$client->name} ({$client->email}) on the Client Portal.")
                ->modalIcon('heroicon-o-key')
                ->form([
                    Forms\Components\TextInput::make('password')
                        ->label('Temporary Password')
                        ->password()
                        ->revealable()
                        ->default(Str::random(10))
                        ->required()
                        ->helperText('The client will be asked to change this on first login.'),
                ])
                ->action(function (array $data) use ($client) {
                    $existing = User::where('email', $client->email)->first();

                    if ($existing) {
                        $client->update(['user_id' => $existing->id]);
                        Notification::make()
                            ->warning()
                            ->title('Linked to existing user')
                            ->body("A user with email {$client->email} already exists. Client linked to that account.")
                            ->send();
                        return;
                    }

                    $user = User::create([
                        'name' => $client->name,
                        'email' => $client->email,
                        'password' => Hash::make($data['password']),
                        'user_type' => 'client',
                        'company_id' => $client->company_id,
                        'must_change_password' => true,
                        'email_verified_at' => now(),
                    ]);

                    $client->update(['user_id' => $user->id]);

                    $loginUrl = config('app.url') . '/client/login';

                    try {
                        // Send welcome email synchronously (contains credentials)
                        app(EmailService::class)->send(
                            'client-welcome',
                            $user,
                            [
                                'client_name' => $client->name,
                                'company_name' => auth()->user()->company?->name ?? config('app.name'),
                                'email' => $client->email,
                                'password' => $data['password'],
                                'login_url' => $loginUrl,
                                'user_name' => $client->name,
                            ],
                            $client->company_id,
                            sync: true,
                        );

                        // Also send invitation email with accept link
                        app(\App\Services\InvitationService::class)->sendInvitation(
                            $user,
                            plainPassword: $data['password'],
                            invitedBy: auth()->id(),
                        );

                        Notification::make()
                            ->success()
                            ->title('Portal access granted!')
                            ->body("Credentials sent to {$client->email}")
                            ->send();
                    } catch (\Exception $e) {
                        Log::warning('Client welcome email failed', ['error' => $e->getMessage()]);

                        Notification::make()
                            ->warning()
                            ->title('Account created — email failed')
                            ->body("Share manually:\n📧 {$client->email}\n🔑 {$data['password']}\n🔗 {$loginUrl}")
                            ->persistent()
                            ->send();
                    }
                }),

            // Portal status indicator
            Actions\Action::make('portalStatus')
                ->label($hasPortalAccess ? 'Portal Active ✓' : 'No Portal Access')
                ->icon($hasPortalAccess ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                ->color($hasPortalAccess ? 'success' : 'gray')
                ->disabled()
                ->extraAttributes(['style' => 'pointer-events:none;opacity:0.7;']),

            Actions\EditAction::make(),
        ];
    }
}
