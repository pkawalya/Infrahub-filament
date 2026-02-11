<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\Company;
use App\Models\User;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|\UnitEnum|null $navigationGroup = 'Platform Management';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('User')->tabs([
                Schemas\Components\Tabs\Tab::make('Account')->schema([
                    Schemas\Components\Section::make('User Information')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
                            ->dehydrated(fn($state) => !empty($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->helperText(fn(string $operation) => $operation === 'edit' ? 'Leave blank to keep existing password' : ''),
                        Forms\Components\Select::make('user_type')
                            ->options(User::$userTypes)
                            ->required()
                            ->default('member'),
                    ])->columns(2),
                ]),

                Schemas\Components\Tabs\Tab::make('Organization')->schema([
                    Schemas\Components\Section::make('Company & Role')->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Leave empty for super admins'),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Forms\Components\TextInput::make('job_title')->maxLength(255),
                        Forms\Components\TextInput::make('department')->maxLength(255),
                        Forms\Components\TextInput::make('phone')->maxLength(50),
                    ])->columns(2),
                ]),

                Schemas\Components\Tabs\Tab::make('Settings')->schema([
                    Schemas\Components\Section::make('Account Settings')->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At'),
                        Forms\Components\Select::make('timezone')
                            ->searchable()
                            ->options(fn() => collect(timezone_identifiers_list())->mapWithKeys(fn($tz) => [$tz => $tz]))
                            ->default('UTC'),
                    ])->columns(2),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('User Details')->tabs([
                Schemas\Components\Tabs\Tab::make('Account')->schema([
                    Schemas\Components\Section::make('User Information')->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Full Name')
                            ->icon('heroicon-o-user'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('user_type')
                            ->label('User Type')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'super_admin' => 'danger',
                                'company_admin' => 'warning',
                                'manager' => 'info',
                                'member' => 'success',
                                'technician' => 'primary',
                                'client' => 'gray',
                                default => 'gray',
                            }),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('Account Active')
                            ->boolean(),
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->dateTime()
                            ->placeholder('Not verified'),
                        Infolists\Components\TextEntry::make('last_login_at')
                            ->label('Last Login')
                            ->dateTime()
                            ->placeholder('Never logged in'),
                    ])->columns(2),
                ]),

                Schemas\Components\Tabs\Tab::make('Organization')->schema([
                    Schemas\Components\Section::make('Company & Role')->schema([
                        Infolists\Components\TextEntry::make('company.name')
                            ->label('Company')
                            ->icon('heroicon-o-building-office')
                            ->placeholder('— No Company —'),
                        Infolists\Components\TextEntry::make('roles.name')
                            ->label('Roles')
                            ->badge()
                            ->color('primary')
                            ->separator(', '),
                        Infolists\Components\TextEntry::make('job_title')
                            ->label('Job Title')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('department')
                            ->label('Department')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('phone')
                            ->label('Phone')
                            ->icon('heroicon-o-phone')
                            ->placeholder('—'),
                    ])->columns(2),
                ]),

                Schemas\Components\Tabs\Tab::make('Activity')->schema([
                    Schemas\Components\Section::make('Timestamps')->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Account Created')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('timezone')
                            ->label('Timezone')
                            ->placeholder('Not set'),
                    ])->columns(3),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user_type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'company_admin' => 'warning',
                        'manager' => 'info',
                        'member' => 'success',
                        'technician' => 'primary',
                        'client' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->placeholder('— No Company —'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->separator(','),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->options(User::$userTypes)
                    ->label('User Type'),
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Company'),
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Roles'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\Filter::make('email_unverified')
                    ->label('Email Unverified')
                    ->query(fn(Builder $query) => $query->whereNull('email_verified_at')),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('toggleActive')
                    ->icon(fn(User $record) => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn(User $record) => $record->is_active ? 'danger' : 'success')
                    ->label(fn(User $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->requiresConfirmation()
                    ->action(fn(User $record) => $record->update(['is_active' => !$record->is_active])),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                Actions\BulkAction::make('activateAll')
                    ->label('Activate Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn($records) => $records->each->update(['is_active' => true])),
                Actions\BulkAction::make('deactivateAll')
                    ->label('Deactivate Selected')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn($records) => $records->each->update(['is_active' => false])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
}
