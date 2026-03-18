<?php

namespace App\Filament\App\Pages\Auth;

use App\Models\Company;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyRegistration extends BaseRegister
{
    protected Width|string|null $maxWidth = '2xl';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Company')
                        ->icon('heroicon-o-building-office-2')
                        ->description('Tell us about your organization')
                        ->schema([
                            TextInput::make('company_name')
                                ->label('Company / Organization Name')
                                ->placeholder('e.g. Omega Construction Ltd')
                                ->required()
                                ->maxLength(255)
                                ->autofocus(),

                            Select::make('industry')
                                ->label('Industry')
                                ->options([
                                    'general_construction' => 'General Construction',
                                    'civil_engineering' => 'Civil Engineering',
                                    'building_construction' => 'Building Construction',
                                    'road_works' => 'Road & Highway Works',
                                    'water_sanitation' => 'Water & Sanitation',
                                    'electrical' => 'Electrical Engineering',
                                    'mechanical' => 'Mechanical Engineering',
                                    'oil_gas' => 'Oil & Gas',
                                    'mining' => 'Mining',
                                    'real_estate' => 'Real Estate Development',
                                    'architecture' => 'Architecture & Design',
                                    'consulting' => 'Consulting / Project Management',
                                    'government' => 'Government / Public Sector',
                                    'other' => 'Other',
                                ])
                                ->required()
                                ->native(false)
                                ->searchable(),

                            TextInput::make('company_phone')
                                ->label('Company Phone')
                                ->tel()
                                ->placeholder('+256 700 000 000'),

                            TextInput::make('company_email')
                                ->label('Company Email')
                                ->email()
                                ->placeholder('info@company.com'),

                            Select::make('company_country')
                                ->label('Country')
                                ->options([
                                    'UG' => '🇺🇬 Uganda',
                                    'KE' => '🇰🇪 Kenya',
                                    'TZ' => '🇹🇿 Tanzania',
                                    'RW' => '🇷🇼 Rwanda',
                                    'SS' => '🇸🇸 South Sudan',
                                    'CD' => '🇨🇩 DR Congo',
                                    'ET' => '🇪🇹 Ethiopia',
                                    'NG' => '🇳🇬 Nigeria',
                                    'GH' => '🇬🇭 Ghana',
                                    'ZA' => '🇿🇦 South Africa',
                                    'GB' => '🇬🇧 United Kingdom',
                                    'US' => '🇺🇸 United States',
                                    'AE' => '🇦🇪 UAE',
                                    'IN' => '🇮🇳 India',
                                    'CN' => '🇨🇳 China',
                                    'OTHER' => '🌍 Other',
                                ])
                                ->required()
                                ->native(false)
                                ->searchable(),

                            Select::make('team_size')
                                ->label('Team Size')
                                ->options([
                                    '1-5' => '1–5 people',
                                    '6-20' => '6–20 people',
                                    '21-50' => '21–50 people',
                                    '51-100' => '51–100 people',
                                    '100+' => '100+ people',
                                ])
                                ->required()
                                ->native(false),
                        ]),

                    Wizard\Step::make('Your Account')
                        ->icon('heroicon-o-user-circle')
                        ->description('Create your admin account')
                        ->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent(),
                            TextInput::make('job_title')
                                ->label('Job Title')
                                ->placeholder('e.g. Project Manager, Director')
                                ->maxLength(100),
                            TextInput::make('phone')
                                ->label('Phone Number')
                                ->tel()
                                ->placeholder('+256 700 000 000'),
                            $this->getPasswordFormComponent(),
                            $this->getPasswordConfirmationFormComponent(),
                        ]),
                ])
                    ->submitAction(view('filament.app.components.onboarding-submit-btn'))
                    ->persistStepInQueryString(),
            ]);
    }

    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();

        // Determine max users from team size
        $maxUsers = match ($data['team_size'] ?? '1-5') {
            '1-5' => 5,
            '6-20' => 20,
            '21-50' => 50,
            '51-100' => 100,
            '100+' => 250,
            default => 10,
        };

        // Create company with 14-day trial
        $company = Company::create([
            'name' => $data['company_name'],
            'slug' => Str::slug($data['company_name']),
            'email' => $data['company_email'] ?? null,
            'phone' => $data['company_phone'] ?? null,
            'country' => $data['company_country'],
            'is_active' => true,
            'is_trial' => true,
            'trial_ends_at' => now()->addDays(14),
            'activated_at' => now(),
            'max_users' => $maxUsers,
            'max_projects' => 5,
            'max_storage_gb' => 5,
            'currency' => 'UGX',
            'currency_symbol' => 'UGX',
            'currency_position' => 'before',
            'timezone' => 'Africa/Kampala',
            'settings' => [
                'industry' => $data['industry'],
                'team_size' => $data['team_size'],
            ],
        ]);

        // Enable default modules
        $defaultModules = ['core', 'cde', 'sheq', 'tasks'];
        foreach ($defaultModules as $code) {
            $company->enableModule($code);
        }

        // Create the admin user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $company->id,
            'user_type' => 'company_admin',
            'job_title' => $data['job_title'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_active' => true,
        ]);

        // Assign company_admin role if it exists
        if ($role = \Spatie\Permission\Models\Role::where('name', 'company_admin')->first()) {
            $user->assignRole($role);
        }

        // Send welcome email to the new company admin
        try {
            $emailService = app(\App\Services\EmailService::class);
            $emailService->send('welcome', $user, [
                'login_url' => url('/app/login'),
            ], $company->id, sync: true);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send registration welcome email: ' . $e->getMessage());
        }

        // Notify the InfraHub team about the new signup
        try {
            \Illuminate\Support\Facades\Mail::raw(
                implode("\n", [
                    '🏗️ NEW COMPANY SIGNUP (Self-Registration)',
                    str_repeat('─', 40),
                    '',
                    'Company: ' . $data['company_name'],
                    'Industry: ' . $data['industry'],
                    'Team Size: ' . ($data['team_size'] ?? 'N/A'),
                    'Country: ' . $data['company_country'],
                    '',
                    'Admin: ' . $data['name'],
                    'Email: ' . $data['email'],
                    'Phone: ' . ($data['phone'] ?? 'N/A'),
                    'Title: ' . ($data['job_title'] ?? 'N/A'),
                    '',
                    str_repeat('─', 40),
                    'Trial started: 14 days',
                    config('app.url') . '/admin/companies',
                ]),
                function ($message) use ($data) {
                    $message->to('info@infrahub.click')
                        ->cc('appcellon@gmail.com')
                        ->replyTo($data['email'], $data['name'])
                        ->subject('🏗️ New Company Signup — ' . $data['company_name']);
                }
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send signup notification: ' . $e->getMessage());
        }

        auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }
}
