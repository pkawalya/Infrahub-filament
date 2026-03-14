<?php

namespace App\Filament\App\Pages;

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
    protected string $view = 'filament.app.pages.change-password';

    // Hide from navigation — only shown when forced
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    /**
     * Determine why the user needs to change their password.
     */
    public string $reason = 'new_account';

    public function mount(): void
    {
        $user = auth()->user();

        // If user doesn't need to change password, redirect to dashboard
        if (!$user->must_change_password) {
            $this->redirect('/app');
            return;
        }

        // Determine reason for the forced change
        $maxAgeDays = config('security.password.max_age_days', 90);
        if ($maxAgeDays > 0 && $user->password_changed_at) {
            $passwordAge = now()->diffInDays($user->password_changed_at);
            if ($passwordAge >= $maxAgeDays) {
                $this->reason = 'expired';
            }
        } elseif ($user->password_changed_at) {
            // Has changed before but flagged — admin forced it
            $this->reason = 'admin_forced';
        }
        // else: no password_changed_at = new account (default)

        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        $config = config('security.password', []);
        $minLength = $config['min_length'] ?? 10;
        $preventReuse = $config['prevent_reuse'] ?? 5;

        $description = match ($this->reason) {
            'expired' => "Your password is older than " . config('security.password.max_age_days', 90) . " days. For security, please choose a new password.",
            'admin_forced' => "Your administrator has required you to change your password before continuing.",
            default => "Your account was recently created. For security, please set a new password before continuing.",
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
                            ->helperText("Min {$minLength} chars with uppercase, lowercase, number, and symbol. Cannot reuse your last {$preventReuse} passwords."),

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

        // ── Verify current password ───────────────────────
        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->body('Please enter your existing password.')
                ->danger()
                ->send();
            return;
        }

        // ── Check password reuse ──────────────────────────
        if ($user->wasPasswordUsedBefore($data['new_password'])) {
            $preventReuse = config('security.password.prevent_reuse', 5);
            Notification::make()
                ->title('Password already used')
                ->body("For security, you cannot reuse any of your last {$preventReuse} passwords. Please choose a different one.")
                ->danger()
                ->send();
            return;
        }

        // ── Update password ───────────────────────────────
        // HasPasswordHistory trait will automatically:
        //   - Record the old hash in password_history
        //   - Set password_changed_at = now()
        //   - Clear must_change_password = false
        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        // Invalidate all other sessions for this user (security)
        Auth::logoutOtherDevices($data['new_password']);

        Notification::make()
            ->title('Password changed successfully! 🎉')
            ->body('Your password has been updated. You can now use the platform securely.')
            ->success()
            ->send();

        $this->redirect('/app');
    }
}
