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

        // ── Build permission options with human-friendly labels ──
        $permQuery = \Spatie\Permission\Models\Permission::query()->orderBy('name');

        // Super-admin-only permissions that company admins should NOT see
        if ($isCompanyAdmin) {
            $permQuery->where(function ($q) {
                $q->where('name', 'not like', '%_company')
                    ->where('name', 'not like', '%_subscription')
                    ->where('name', 'not like', '%_role')
                    ->where('name', 'not like', '%_email::template')
                    ->where('name', 'not like', '%_company::%')
                    ->where('name', 'not like', 'page_SystemSettings')
                    ->where('name', 'not like', 'widget_PlatformOverview')
                    ->where('name', 'not like', 'widget_CompaniesByPlanChart')
                    ->where('name', 'not like', 'widget_UserGrowthChart');
            });
        }

        $permOptions = $permQuery->pluck('name', 'id')
            ->mapWithKeys(fn(string $name, int $id) => [$id => self::formatPermissionLabel($name)])
            ->toArray();

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
                    ->options($permOptions)
                    ->columns(3)
                    ->searchable()
                    ->bulkToggleable()
                    ->helperText('Select the permissions this role should have'),
            ]),
        ]);
    }

    /**
     * Format raw permission name into a human-readable label.
     * e.g. 'create_cde::project' → 'Create Project'
     *      'view_any_work::order' → 'View Any Work Order'
     *      'page_SystemSettings' → 'Page: System Settings'
     *      'widget_ProjectProgressChart' → 'Widget: Project Progress Chart'
     */
    private static function formatPermissionLabel(string $name): string
    {
        // Widgets
        if (str_starts_with($name, 'widget_')) {
            $widget = Str::headline(str_replace('widget_', '', $name));
            return "Widget: {$widget}";
        }

        // Pages
        if (str_starts_with($name, 'page_')) {
            $page = Str::headline(str_replace('page_', '', $name));
            return "Page: {$page}";
        }

        // Resource permissions: action_resource or action_any_resource
        // Replace :: with _ for processing, then humanize
        $clean = str_replace('::', '_', $name);

        // Resource name mappings for friendlier display
        $resourceMap = [
            'cde_project' => 'Project',
            'safety_incident' => 'Safety Incident',
            'work_order' => 'Work Order',
            'email_template' => 'Email Template',
            'company_email_template' => 'Company Email Template',
            'company_role' => 'Company Role',
            'company_user' => 'Company User',
        ];

        // Try to extract action and resource
        $parts = explode('_', $clean);
        $action = ucfirst($parts[0]); // create, view, update, delete, etc.

        // Handle 'force_delete', 'force_delete_any', 'view_any', 'restore_any'
        $rest = implode('_', array_slice($parts, 1));
        if (str_starts_with($rest, 'delete_any_')) {
            $action = 'Force Delete Any';
            $rest = substr($rest, 11);
        } elseif (str_starts_with($rest, 'delete_')) {
            $action = 'Force Delete';
            $rest = substr($rest, 7);
        } elseif (str_starts_with($rest, 'any_')) {
            $action = $action . ' Any';
            $rest = substr($rest, 4);
        } elseif ($parts[0] === 'force') {
            $action = 'Force Delete';
            $rest = implode('_', array_slice($parts, 2));
            if (str_starts_with($rest, 'any_')) {
                $action = 'Force Delete Any';
                $rest = substr($rest, 4);
            }
        }

        $resource = $resourceMap[$rest] ?? Str::headline($rest);

        return trim("{$action} {$resource}");
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
                    ->formatStateUsing(fn(string $state): string => self::formatPermissionLabel($state))
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
