<?php

namespace App\Filament\Client\Pages;

use App\Rules\StrongPassword;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use BackedEnum;
use UnitEnum;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';
    protected static ?string $title = 'Change Password';
    protected static ?string $navigationLabel = 'Change Password';
    protected static ?string $slug = 'change-password';
    protected string $view = 'filament.client.pages.change-password';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];
    public string $reason = 'new_account';

    public function mount(): void
    {
        $user = auth()->user();

        if (!$user->must_change_password) {
            $this->redirect('/client');
            return;
        }

        $maxAgeDays = config('security.password.max_age_days', 90);
        if ($maxAgeDays > 0 && $user->password_changed_at) {
            $passwordAge = now()->diffInDays($user->password_changed_at);
            if ($passwordAge >= $maxAgeDays) {
                $this->reason = 'expired';
            }
        } elseif ($user->password_changed_at) {
            $this->reason = 'admin_forced';
        }

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $minLength = config('security.password.min_length', 10);

        $description = match ($this->reason) {
            'expired' => "Your password is older than " . config('security.password.max_age_days', 90) . " days. Please choose a new password.",
            'admin_forced' => "A password change has been required for your account.",
            default => "Please set a new password before continuing.",
        };

        return $schema
            ->components([
                Section::make('Set Your New Password')
                    ->description($description)
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required()
                            ->revealable(),

                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->revealable()
                            ->rule(new StrongPassword())
                            ->different('current_password')
                            ->helperText("Min {$minLength} chars with uppercase, lowercase, number, and symbol."),

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

        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->danger()
                ->send();
            return;
        }

        if ($user->wasPasswordUsedBefore($data['new_password'])) {
            $preventReuse = config('security.password.prevent_reuse', 5);
            Notification::make()
                ->title('Password already used')
                ->body("Cannot reuse your last {$preventReuse} passwords.")
                ->danger()
                ->send();
            return;
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        Auth::logoutOtherDevices($data['new_password']);

        Notification::make()
            ->title('Password changed successfully! 🎉')
            ->success()
            ->send();

        $this->redirect('/client');
    }
}
