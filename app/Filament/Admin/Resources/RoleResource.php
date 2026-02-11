<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null $navigationGroup = 'Platform Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Roles & Permissions';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Role Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Use snake_case format, e.g. company_admin'),
                Forms\Components\TextInput::make('guard_name')
                    ->default('web')
                    ->required()
                    ->maxLength(255),
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
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label('Last Updated')
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (Role $record) {
                        if ($record->name === 'super_admin') {
                            throw new \Exception('Cannot delete the super_admin role.');
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Role::count();
    }
}
