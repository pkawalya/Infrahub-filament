<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CompanyUserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class CompanyUserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $slug = 'settings/users';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'Users';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isCompanyAdmin());
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery();

        // Company admins only see their own company's users
        if ($user && $user->isCompanyAdmin() && !$user->isSuperAdmin()) {
            $query->where('company_id', $user->company_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $isCompanyAdmin = $user && $user->isCompanyAdmin() && !$user->isSuperAdmin();

        return $schema->schema([
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
                    ->options(function () use ($isCompanyAdmin) {
                        $types = User::$userTypes;
                        if ($isCompanyAdmin) {
                            // Company admins can't create super admins
                            unset($types['super_admin']);
                        }
                        return $types;
                    })
                    ->required()
                    ->default('member'),
            ])->columns(2),

            Schemas\Components\Section::make('Role & Details')->schema([
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name', function (Builder $query) {
                        $user = auth()->user();
                        if ($user && $user->isCompanyAdmin() && !$user->isSuperAdmin()) {
                            $query->where('company_id', $user->company_id)
                                ->orWhereNull('company_id');
                        }
                    })
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Forms\Components\TextInput::make('job_title')->maxLength(255),
                Forms\Components\TextInput::make('department')->maxLength(255),
                Forms\Components\TextInput::make('phone')->maxLength(50),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2),

            // Hidden company_id field — auto-set for company admins
            Forms\Components\Hidden::make('company_id')
                ->default(fn() => auth()->user()?->company_id),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('User Information')->schema([
                Infolists\Components\TextEntry::make('name')
                    ->label('Full Name')
                    ->icon('heroicon-o-user'),
                Infolists\Components\TextEntry::make('email')
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
                        default => 'gray',
                    }),
                Infolists\Components\IconEntry::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])->columns(2),

            Schemas\Components\Section::make('Role & Activity')->schema([
                Infolists\Components\TextEntry::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->separator(', ')
                    ->placeholder('No roles assigned'),
                Infolists\Components\TextEntry::make('job_title')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('department')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('phone')
                    ->icon('heroicon-o-phone')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->placeholder('Never'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Joined')
                    ->dateTime(),
            ])->columns(2),
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
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->options(User::$userTypes)
                    ->label('User Type'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyUsers::route('/'),
            'create' => Pages\CreateCompanyUser::route('/create'),
            'view' => Pages\ViewCompanyUser::route('/{record}'),
            'edit' => Pages\EditCompanyUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $company = auth()->user()?->company;
        $count = (int) static::getEloquentQuery()->count();

        if ($company) {
            $limit = $company->getEffectiveMaxUsers();
            if ($limit)
                return "{$count}/{$limit}";
        }

        return (string) $count;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $company = auth()->user()?->company;
        if (!$company)
            return 'primary';

        $limit = $company->getEffectiveMaxUsers();
        if (!$limit)
            return 'primary';

        $count = $company->users()->count();
        if ($count >= $limit)
            return 'danger';
        if ($count >= $limit * 0.8)
            return 'warning';
        return 'primary';
    }
}
