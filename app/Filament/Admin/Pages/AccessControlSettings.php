<?php

namespace App\Filament\Admin\Pages;

use App\Models\BlockedIp;
use App\Models\Setting;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccessControlSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = 'Access Control';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 11;
    protected static ?string $title = 'Access Control Settings';
    protected static ?string $slug = 'access-control-settings';
    protected string $view = 'filament.admin.pages.access-control-settings';

    public ?array $data = [];

    /**
     * ISO 3166-1 alpha-2 country code list for the dropdown.
     */
    protected static function countryOptions(): array
    {
        return [
            'UG' => '🇺🇬 Uganda',
            'KE' => '🇰🇪 Kenya',
            'TZ' => '🇹🇿 Tanzania',
            'RW' => '🇷🇼 Rwanda',
            'SS' => '🇸🇸 South Sudan',
            'NG' => '🇳🇬 Nigeria',
            'GH' => '🇬🇭 Ghana',
            'ZA' => '🇿🇦 South Africa',
            'ET' => '🇪🇹 Ethiopia',
            'EG' => '🇪🇬 Egypt',
            'GB' => '🇬🇧 United Kingdom',
            'US' => '🇺🇸 United States',
            'AE' => '🇦🇪 UAE',
            'SA' => '🇸🇦 Saudi Arabia',
            'IN' => '🇮🇳 India',
            'CN' => '🇨🇳 China',
            'JP' => '🇯🇵 Japan',
            'DE' => '🇩🇪 Germany',
            'FR' => '🇫🇷 France',
            'CA' => '🇨🇦 Canada',
            'AU' => '🇦🇺 Australia',
            'BR' => '🇧🇷 Brazil',
            'SG' => '🇸🇬 Singapore',
            'NL' => '🇳🇱 Netherlands',
            'SE' => '🇸🇪 Sweden',
            'NO' => '🇳🇴 Norway',
            'CH' => '🇨🇭 Switzerland',
            'IT' => '🇮🇹 Italy',
            'ES' => '🇪🇸 Spain',
            'PL' => '🇵🇱 Poland',
            'CD' => '🇨🇩 DR Congo',
            'MZ' => '🇲🇿 Mozambique',
            'MW' => '🇲🇼 Malawi',
            'ZM' => '🇿🇲 Zambia',
            'ZW' => '🇿🇼 Zimbabwe',
            'BW' => '🇧🇼 Botswana',
            'NA' => '🇳🇦 Namibia',
            'BI' => '🇧🇮 Burundi',
            'SO' => '🇸🇴 Somalia',
            'SD' => '🇸🇩 Sudan',
        ];
    }

    public function mount(): void
    {
        $geoConfig = config('security.geo_access', []);
        $ipConfig = config('security.ip_blocking', []);

        // Load from database settings (override config/env)
        $geoEnabled = Setting::getValue('geo_restriction_enabled', $geoConfig['enabled'] ?? false);
        $allowedCountries = Setting::getValue('geo_allowed_countries');
        $ipBlockingEnabled = Setting::getValue('ip_blocking_enabled', $ipConfig['enabled'] ?? true);
        $whitelistedIps = Setting::getValue('whitelisted_ips');

        $this->form->fill([
            'geo_enabled' => filter_var($geoEnabled, FILTER_VALIDATE_BOOLEAN),
            'allowed_countries' => $allowedCountries
                ? (is_string($allowedCountries) ? explode(',', $allowedCountries) : $allowedCountries)
                : ($geoConfig['allowed_countries'] ?? []),
            'geo_cache_minutes' => (int) Setting::getValue('geo_cache_minutes', $geoConfig['cache_minutes'] ?? 1440),
            'geo_block_message' => Setting::getValue('geo_block_message', $geoConfig['block_message'] ?? 'Access to this service is not available in your region.'),
            'ip_blocking_enabled' => filter_var($ipBlockingEnabled, FILTER_VALIDATE_BOOLEAN),
            'whitelisted_ips' => $whitelistedIps
                ? (is_string($whitelistedIps) ? explode(',', $whitelistedIps) : $whitelistedIps)
                : ($ipConfig['whitelisted_ips'] ?? ['127.0.0.1']),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Geo Restriction ──────────────────────────
                Section::make('🌍 Geo Restriction')
                    ->description('Restrict access to specific countries. Visitors from unlisted countries will see a 403 error.')
                    ->schema([
                        Toggle::make('geo_enabled')
                            ->label('Enable Geo Restriction')
                            ->helperText('When enabled, only visitors from allowed countries can access the platform.')
                            ->live(),

                        Select::make('allowed_countries')
                            ->label('Allowed Countries')
                            ->multiple()
                            ->searchable()
                            ->options(static::countryOptions())
                            ->helperText('Select which countries can access InfraHub. Leave empty to allow all.')
                            ->visible(fn($get) => $get('geo_enabled')),

                        TextInput::make('geo_cache_minutes')
                            ->label('Cache Duration (minutes)')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(10080)
                            ->default(1440)
                            ->helperText('How long to cache country lookups per IP. Default: 1440 (24 hours).')
                            ->suffix('minutes')
                            ->visible(fn($get) => $get('geo_enabled')),

                        Textarea::make('geo_block_message')
                            ->label('Block Message')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Message shown to visitors from restricted countries.')
                            ->visible(fn($get) => $get('geo_enabled')),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ── IP Blocking ──────────────────────────────
                Section::make('🛡️ IP Blocking')
                    ->description('Block or whitelist specific IP addresses. Blocked IPs can also be managed via Admin → System → Blocked IPs.')
                    ->schema([
                        Toggle::make('ip_blocking_enabled')
                            ->label('Enable IP Blocking')
                            ->helperText('When enabled, blocked IPs from the database and .env BLOCKED_IPS list are enforced.'),

                        TagsInput::make('whitelisted_ips')
                            ->label('Whitelisted IPs')
                            ->placeholder('Add IP address…')
                            ->helperText('These IPs can never be blocked (e.g. your office IP). One per tag.'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // ── Quick Block ──────────────────────────────
                Section::make('⚡ Quick Block an IP')
                    ->description('Block an IP directly from here without going to the Blocked IPs resource.')
                    ->schema([
                        TextInput::make('quick_block_ip')
                            ->label('IP Address or CIDR')
                            ->placeholder('e.g. 192.168.1.100 or 10.0.0.0/8'),

                        TextInput::make('quick_block_reason')
                            ->label('Reason')
                            ->placeholder('e.g. Suspicious brute-force activity'),

                        Select::make('quick_block_duration')
                            ->label('Duration')
                            ->options([
                                '1' => '1 hour',
                                '6' => '6 hours',
                                '24' => '24 hours',
                                '72' => '3 days',
                                '168' => '7 days',
                                '720' => '30 days',
                                '' => 'Permanent',
                            ])
                            ->default('24'),
                    ])
                    ->columns(3)
                    ->collapsible(),

            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->data;

        // Save geo settings
        Setting::setValue('geo_restriction_enabled', $data['geo_enabled'] ? '1' : '0', 'security');
        Setting::setValue('geo_allowed_countries', implode(',', $data['allowed_countries'] ?? []), 'security');
        Setting::setValue('geo_cache_minutes', (string) ($data['geo_cache_minutes'] ?? 1440), 'security');
        Setting::setValue('geo_block_message', $data['geo_block_message'] ?? '', 'security');

        // Save IP settings
        Setting::setValue('ip_blocking_enabled', $data['ip_blocking_enabled'] ? '1' : '0', 'security');
        Setting::setValue('whitelisted_ips', implode(',', $data['whitelisted_ips'] ?? []), 'security');

        Notification::make()
            ->title('Access Control Settings Saved')
            ->body('Geo-restriction and IP blocking settings have been updated.')
            ->success()
            ->send();
    }

    public function quickBlock(): void
    {
        $ip = $this->data['quick_block_ip'] ?? null;

        if (empty($ip)) {
            Notification::make()
                ->title('IP Address Required')
                ->body('Enter an IP address or CIDR range to block.')
                ->danger()
                ->send();
            return;
        }

        $cidr = str_contains($ip, '/') ? $ip : null;
        $ipAddress = str_contains($ip, '/') ? explode('/', $ip)[0] : $ip;
        $hours = $this->data['quick_block_duration'] ?? null;

        BlockedIp::updateOrCreate(
            ['ip_address' => $ipAddress, 'cidr_range' => $cidr],
            [
                'reason' => $this->data['quick_block_reason'] ?? 'Blocked from admin settings',
                'blocked_by' => auth()->user()?->name ?? 'admin',
                'expires_at' => $hours ? now()->addHours((int) $hours) : null,
                'is_active' => true,
            ]
        );

        BlockedIp::clearCache($ipAddress);

        // Clear the quick-block fields
        $this->data['quick_block_ip'] = '';
        $this->data['quick_block_reason'] = '';

        $expiry = $hours ? now()->addHours((int) $hours)->diffForHumans() : 'permanent';

        Notification::make()
            ->title('IP Blocked')
            ->body("Blocked {$ip} ({$expiry})")
            ->success()
            ->send();
    }
}
