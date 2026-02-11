<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CompanyRoleResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Models\Role;

class CompanyRoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $slug = 'settings/roles';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Roles';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isCompanyAdmin());
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user && $user->isCompanyAdmin() && !$user->isSuperAdmin()) {
            // Company admins see: their company's roles + global roles (for reference)
            $query->where(function ($q) use ($user) {
                $q->where('company_id', $user->company_id)
                    ->orWhereNull('company_id');
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $isCompanyAdmin = $user && $user->isCompanyAdmin() && !$user->isSuperAdmin();

        return $schema->schema([
            Schemas\Components\Section::make('Role Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Use snake_case format, e.g. project_manager'),
                Forms\Components\TextInput::make('guard_name')
                    ->default('web')
                    ->required()
                    ->maxLength(255)
                    ->disabled($isCompanyAdmin)
                    ->dehydrated(),
                Forms\Components\Hidden::make('company_id')
                    ->default(fn() => auth()->user()?->company_id),
            ])->columns(2),

            Schemas\Components\Section::make('Permissions')->schema([
                Forms\Components\CheckboxList::make('permissions')
                    ->relationship('permissions', 'name')
                    ->columns(3)
                    ->searchable()
                    ->bulkToggleable()
                    ->helperText('Select the permissions this role should have'),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Role Details')->schema([
                Infolists\Components\TextEntry::make('name')
                    ->label('Role Name')
                    ->formatStateUsing(fn(string $state): string => Str::headline($state))
                    ->icon('heroicon-o-shield-check'),
                Infolists\Components\TextEntry::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('warning'),
                Infolists\Components\TextEntry::make('company.name')
                    ->label('Company')
                    ->placeholder('Global (System Role)')
                    ->badge()
                    ->color('info'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ])->columns(2),

            Schemas\Components\Section::make('Assigned Permissions')->schema([
                Infolists\Components\TextEntry::make('permissions.name')
                    ->label('Permissions')
                    ->badge()
                    ->color('primary')
                    ->separator(', ')
                    ->placeholder('No permissions assigned'),
            ]),

            Schemas\Components\Section::make('Users with this Role')->schema([
                Infolists\Components\TextEntry::make('users.name')
                    ->label('Users')
                    ->badge()
                    ->color('success')
                    ->separator(', ')
                    ->placeholder('No users assigned to this role'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $isCompanyAdmin = $user && $user->isCompanyAdmin() && !$user->isSuperAdmin();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight(FontWeight::Medium)
                    ->formatStateUsing(fn(string $state): string => Str::headline($state))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Scope')
                    ->placeholder('Global')
                    ->badge()
                    ->color(fn($state) => $state ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label('Permissions')
                    ->counts('permissions')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('users_count')
                    ->badge()
                    ->label('Users')
                    ->counts('users')
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    // Company admins can only edit their own company's roles
                    ->visible(fn(Role $record) => !$isCompanyAdmin || $record->company_id === $user?->company_id),
                Actions\DeleteAction::make()
                    ->visible(fn(Role $record) => !$isCompanyAdmin || $record->company_id === $user?->company_id)
                    ->before(function (Role $record) {
                        if (in_array($record->name, ['super_admin', 'panel_user'])) {
                            throw new \Exception('Cannot delete system roles.');
                        }
                    }),
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
            'index' => Pages\ListCompanyRoles::route('/'),
            'create' => Pages\CreateCompanyRole::route('/create'),
            'view' => Pages\ViewCompanyRole::route('/{record}'),
            'edit' => Pages\EditCompanyRole::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }
}
