<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use BackedEnum;
use UnitEnum;

class EmailSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $title = 'Email Settings';
    protected static ?int $navigationSort = 20;
    protected string $view = 'filament.admin.pages.email-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'mail_mailer' => Setting::getValue('mail_mailer', config('mail.default')),
            'mail_host' => Setting::getValue('mail_host', config('mail.mailers.smtp.host')),
            'mail_port' => Setting::getValue('mail_port', config('mail.mailers.smtp.port')),
            'mail_username' => Setting::getValue('mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => Setting::getValue('mail_password', ''),
            'mail_encryption' => Setting::getValue('mail_encryption', config('mail.mailers.smtp.encryption', 'tls')),
            'mail_from_address' => Setting::getValue('mail_from_address', config('mail.from.address')),
            'mail_from_name' => Setting::getValue('mail_from_name', config('mail.from.name')),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('SMTP Configuration')
                    ->description('Configure your outgoing mail server. Gmail, Outlook, or any SMTP provider.')
                    ->icon('heroicon-o-server-stack')
                    ->schema([
                        Select::make('mail_mailer')
                            ->label('Mail Driver')
                            ->options([
                                'smtp' => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'log' => 'Log (Testing)',
                            ])
                            ->required()
                            ->default('smtp'),

                        TextInput::make('mail_host')
                            ->label('SMTP Host')
                            ->placeholder('smtp.gmail.com')
                            ->required(),

                        TextInput::make('mail_port')
                            ->label('SMTP Port')
                            ->placeholder('587')
                            ->numeric()
                            ->required(),

                        TextInput::make('mail_username')
                            ->label('Username / Email')
                            ->placeholder('your-email@gmail.com')
                            ->required(),

                        TextInput::make('mail_password')
                            ->label('Password / App Password')
                            ->password()
                            ->revealable()
                            ->required(),

                        Select::make('mail_encryption')
                            ->label('Encryption')
                            ->options([
                                'auto' => 'Auto (Recommended for port 587)',
                                'smtps' => 'SSL/TLS (Port 465)',
                                'none' => 'None',
                            ])
                            ->default('auto')
                            ->required()
                            ->helperText('Auto = STARTTLS on port 587 (Gmail, Outlook)'),
                    ])
                    ->columns(2),

                Section::make('Sender Identity')
                    ->description('The "From" name and address that recipients see.')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('mail_from_address')
                            ->label('From Email Address')
                            ->email()
                            ->placeholder('noreply@infrahub.click')
                            ->required(),

                        TextInput::make('mail_from_name')
                            ->label('From Name')
                            ->placeholder('InfraHub')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value, 'email');
        }

        Notification::make()
            ->title('Email Settings Saved')
            ->body('SMTP configuration updated successfully.')
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        $data = $this->form->getState();

        try {
            // Map encryption to Symfony Mailer scheme
            $scheme = match ($data['mail_encryption']) {
                'smtps' => 'smtps',
                'none' => 'smtp',
                default => null, // auto = let Symfony handle STARTTLS
            };

            // Temporarily override mail config
            config([
                'mail.default' => $data['mail_mailer'],
                'mail.mailers.smtp.host' => $data['mail_host'],
                'mail.mailers.smtp.port' => (int) $data['mail_port'],
                'mail.mailers.smtp.username' => $data['mail_username'],
                'mail.mailers.smtp.password' => $data['mail_password'],
                'mail.mailers.smtp.scheme' => $scheme,
                'mail.from.address' => $data['mail_from_address'],
                'mail.from.name' => $data['mail_from_name'],
            ]);

            $userEmail = auth()->user()->email;
            $userName = auth()->user()->name ?? 'Admin';

            Mail::send('emails.test-email', [
                'recipientName' => $userName,
                'smtpHost' => $data['mail_host'],
                'smtpPort' => $data['mail_port'],
                'fromAddress' => $data['mail_from_address'],
                'sentAt' => now()->format('M d, Y \a\t H:i:s'),
                'settingsUrl' => url('/admin/email-settings'),
            ], function ($message) use ($userEmail, $data) {
                $message->to($userEmail)
                    ->subject('InfraHub - Email Configuration Test ✅')
                    ->from($data['mail_from_address'], $data['mail_from_name']);
            });

            Notification::make()
                ->title('Test Email Sent! ✅')
                ->body("Check your inbox at {$userEmail}")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Email Failed ❌')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
