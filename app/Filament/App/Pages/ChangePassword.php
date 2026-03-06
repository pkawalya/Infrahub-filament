<?php

namespace App\Filament\App\Pages;

use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use BackedEnum;
use UnitEnum;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';
    protected static ?string $title = 'Change Password';
    protected static ?string $navigationLabel = 'Change Password';
    protected static ?string $slug = 'change-password';
    protected string $view = 'filament.app.pages.change-password';

    // Hide from navigation — only shown when forced
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        // If user doesn't need to change password, redirect to dashboard
        if (!$user->must_change_password) {
            $this->redirect('/app');
            return;
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Set Your New Password')
                    ->description('Your account was recently created. For security, please set a new password before continuing.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password (from your welcome email)')
                            ->password()
                            ->required()
                            ->revealable(),

                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->revealable()
                            ->rule(Password::min(8)->mixedCase()->numbers())
                            ->different('current_password')
                            ->helperText('At least 8 characters, with uppercase, lowercase, and a number.'),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->required()
                            ->revealable()
                            ->same('new_password'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Verify current password
        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->body('Please enter the password from your welcome email.')
                ->danger()
                ->send();
            return;
        }

        // Update password and clear the flag
        $user->update([
            'password' => Hash::make($data['new_password']),
            'must_change_password' => false,
        ]);

        Notification::make()
            ->title('Password changed successfully! 🎉')
            ->body('Welcome to ' . config('app.name') . '. You can now use the platform.')
            ->success()
            ->send();

        $this->redirect('/app');
    }
}
