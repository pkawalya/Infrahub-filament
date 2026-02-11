<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Models\Module;
use App\Models\Subscription;
use App\Support\Countries;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static string|\UnitEnum|null $navigationGroup = 'Platform Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Company')->tabs([
                Schemas\Components\Tabs\Tab::make('General')->schema([
                    Schemas\Components\Section::make('Company Information')->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('slug')->maxLength(255)->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->maxLength(50),
                        Forms\Components\TextInput::make('website')->url()->maxLength(255),
                        Forms\Components\Textarea::make('address')->rows(2),
                        Schemas\Components\Grid::make(3)->schema([
                            Forms\Components\Select::make('country')
                                ->options(Countries::list())
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(fn(\Filament\Schemas\Components\Utilities\Set $set) => $set('city', null)),
                            Forms\Components\Select::make('city')
                                ->options(fn(\Filament\Schemas\Components\Utilities\Get $get): array => Countries::citiesFor($get('country')))
                                ->searchable()
                                ->required(false),
                            Forms\Components\TextInput::make('state'),
                        ]),
                    ])->columns(2),

                    // Company Admin  — only on create
                    Schemas\Components\Section::make('Company Administrator')
                        ->description('Create the initial admin user for this company.')
                        ->icon('heroicon-o-user-plus')
                        ->schema([
                            Forms\Components\TextInput::make('admin_name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('admin_email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique('users', 'email')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('admin_password')
                                ->label('Password')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->maxLength(255),
                            Forms\Components\TextInput::make('admin_phone')
                                ->label('Phone')
                                ->maxLength(50),
                        ])
                        ->columns(2)
                        ->visible(fn(string $operation): bool => $operation === 'create'),
                ]),

                Schemas\Components\Tabs\Tab::make('Subscription')->schema([
                    Schemas\Components\Section::make('Plan & Billing')->schema([
                        Forms\Components\Select::make('subscription_id')
                            ->relationship('subscription', 'name')
                            ->preload(),
                        Forms\Components\Select::make('billing_cycle')
                            ->options(['monthly' => 'Monthly', 'yearly' => 'Yearly', 'unlimited' => 'Unlimited']),
                        Forms\Components\DateTimePicker::make('subscription_starts_at'),
                        Forms\Components\DateTimePicker::make('subscription_expires_at'),
                    ])->columns(2),

                    Schemas\Components\Section::make('Limits')->schema([
                        Forms\Components\TextInput::make('max_users')->numeric()->default(5),
                        Forms\Components\TextInput::make('max_projects')->numeric()->default(10),
                        Forms\Components\TextInput::make('max_storage_gb')->numeric()->default(5),
                    ])->columns(3),
                ]),

                Schemas\Components\Tabs\Tab::make('Settings')->schema([
                    Schemas\Components\Section::make('Branding')->schema([
                        Forms\Components\FileUpload::make('logo')->image()->directory('companies/logos'),
                        Forms\Components\FileUpload::make('favicon')->image()->directory('companies/favicons'),
                        Forms\Components\ColorPicker::make('primary_color'),
                        Forms\Components\ColorPicker::make('secondary_color'),
                    ])->columns(2),

                    Schemas\Components\Section::make('Locale')->schema([
                        Forms\Components\Select::make('timezone')->searchable()
                            ->options(fn() => collect(timezone_identifiers_list())->mapWithKeys(fn($tz) => [$tz => $tz])),
                        Forms\Components\Select::make('currency')
                            ->options(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'UGX' => 'UGX', 'KES' => 'KES']),
                        Forms\Components\TextInput::make('currency_symbol')->maxLength(5),
                    ])->columns(3),
                ]),

                Schemas\Components\Tabs\Tab::make('Modules')
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        Schemas\Components\Section::make('Company Modules')
                            ->description('Select which modules this company has access to. Projects within this company can then enable a subset of these modules.')
                            ->schema([
                                Forms\Components\CheckboxList::make('enabled_modules')
                                    ->label('Available Modules')
                                    ->options(
                                        collect(Module::$availableModules)
                                            ->mapWithKeys(fn($def, $code) => [$code => $def['name']])
                                            ->toArray()
                                    )
                                    ->descriptions(
                                        collect(Module::$availableModules)
                                            ->mapWithKeys(fn($def, $code) => [$code => $def['description']])
                                            ->toArray()
                                    )
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                                    ->afterStateHydrated(function (Forms\Components\CheckboxList $component, $record) {
                                        if ($record) {
                                            $component->state($record->getEnabledModules());
                                        }
                                    }),
                            ]),
                    ]),

                Schemas\Components\Tabs\Tab::make('Status')->schema([
                    Forms\Components\Toggle::make('is_active')->label('Active'),
                    Forms\Components\Toggle::make('is_trial')->label('Trial'),
                    Forms\Components\DateTimePicker::make('trial_ends_at'),
                    Forms\Components\Textarea::make('notes')->rows(3),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Company Details')->tabs([
                Schemas\Components\Tabs\Tab::make('General')->schema([
                    Schemas\Components\Section::make('Company Information')->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Company Name')
                            ->icon('heroicon-o-building-office'),
                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Phone')
                            ->icon('heroicon-o-phone')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('website')
                            ->label('Website')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('address')
                            ->label('Address')
                            ->placeholder('—'),
                    ])->columns(2),

                    Schemas\Components\Section::make('Location')->schema([
                        Infolists\Components\TextEntry::make('city')->placeholder('—'),
                        Infolists\Components\TextEntry::make('state')->placeholder('—'),
                        Infolists\Components\TextEntry::make('country')->placeholder('—'),
                    ])->columns(3),
                ]),

                Schemas\Components\Tabs\Tab::make('Subscription')->schema([
                    Schemas\Components\Section::make('Plan & Billing')->schema([
                        Infolists\Components\TextEntry::make('subscription.name')
                            ->label('Current Plan')
                            ->badge()
                            ->color('primary')
                            ->placeholder('No plan'),
                        Infolists\Components\TextEntry::make('billing_cycle')
                            ->label('Billing Cycle')
                            ->badge()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('subscription_starts_at')
                            ->label('Subscription Started')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('subscription_expires_at')
                            ->label('Expires At')
                            ->dateTime()
                            ->placeholder('—'),
                    ])->columns(2),

                    Schemas\Components\Section::make('Usage Limits')->schema([
                        Infolists\Components\TextEntry::make('max_users')
                            ->label('Max Users')
                            ->icon('heroicon-o-users'),
                        Infolists\Components\TextEntry::make('max_projects')
                            ->label('Max Projects')
                            ->icon('heroicon-o-rectangle-stack'),
                        Infolists\Components\TextEntry::make('max_storage_gb')
                            ->label('Max Storage (GB)')
                            ->icon('heroicon-o-circle-stack'),
                    ])->columns(3),

                    Schemas\Components\Section::make('Current Usage')->schema([
                        Infolists\Components\TextEntry::make('users_count')
                            ->label('Active Users')
                            ->getStateUsing(fn(Company $record) => $record->users()->count())
                            ->badge()
                            ->color('success'),
                    ])->columns(3),
                ]),

                Schemas\Components\Tabs\Tab::make('Settings')->schema([
                    Schemas\Components\Section::make('Branding')->schema([
                        Infolists\Components\ImageEntry::make('logo')
                            ->label('Logo'),
                        Infolists\Components\ImageEntry::make('favicon')
                            ->label('Favicon'),
                        Infolists\Components\ColorEntry::make('primary_color')
                            ->label('Primary Color'),
                        Infolists\Components\ColorEntry::make('secondary_color')
                            ->label('Secondary Color'),
                    ])->columns(2),

                    Schemas\Components\Section::make('Locale')->schema([
                        Infolists\Components\TextEntry::make('timezone')
                            ->label('Timezone')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('currency')
                            ->label('Currency')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('currency_symbol')
                            ->label('Currency Symbol')
                            ->placeholder('—'),
                    ])->columns(3),
                ]),

                Schemas\Components\Tabs\Tab::make('Modules')
                    ->icon('heroicon-o-puzzle-piece')
                    ->schema([
                        Schemas\Components\Section::make('Enabled Modules')->schema([
                            Infolists\Components\TextEntry::make('moduleAccess')
                                ->label('Active Modules')
                                ->getStateUsing(function (Company $record) {
                                    $enabled = $record->getEnabledModules();
                                    if (empty($enabled))
                                        return 'No modules enabled';

                                    return collect($enabled)
                                        ->map(fn($code) => Module::$availableModules[$code]['name'] ?? $code)
                                        ->join(', ');
                                })
                                ->columnSpanFull(),
                            Infolists\Components\TextEntry::make('projects_count')
                                ->label('Projects Using Modules')
                                ->getStateUsing(fn(Company $record) => $record->projects()->count())
                                ->badge()
                                ->color('primary'),
                        ])->columns(2),
                    ]),

                Schemas\Components\Tabs\Tab::make('Status')->schema([
                    Schemas\Components\Section::make('Account Status')->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                        Infolists\Components\IconEntry::make('is_trial')
                            ->label('Trial')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('trial_ends_at')
                            ->label('Trial Ends At')
                            ->dateTime()
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('No notes')
                            ->columnSpanFull(),
                    ])->columns(3),

                    Schemas\Components\Section::make('Timestamps')->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                    ])->columns(2),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('subscription.name')->label('Plan')->badge(),
                Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Users'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_trial')->boolean(),
                Tables\Columns\TextColumn::make('subscription_expires_at')->dateTime()->label('Expires'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_trial'),
                Tables\Filters\SelectFilter::make('subscription_id')
                    ->relationship('subscription', 'name'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(Company $record) => $record->is_active)
                    ->action(fn(Company $record) => $record->suspend('Suspended by admin')),
                Actions\Action::make('activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(Company $record) => !$record->is_active)
                    ->action(fn(Company $record) => $record->activate()),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
