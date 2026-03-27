<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CdeProjectResource\Pages;
use App\Filament\App\Resources\CdeProjectResource\Pages\Modules;
use App\Filament\Concerns\UIStandards;
use App\Models\CdeProject;
use App\Models\Module;
use App\Support\StoragePath;
use Filament\Actions;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use App\Support\CurrencyHelper;

use Illuminate\Database\Eloquent\Builder;

class CdeProjectResource extends Resource
{
    protected static ?string $model = CdeProject::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Projects';
    protected static ?string $label = 'Project';
    protected static ?int $navigationSort = 1;
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    /**
     * Scope all project queries to the current user's company.
     * Defense-in-depth alongside BelongsToCompany global scope.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && !$user->isSuperAdmin()) {
            $query->where('cde_projects.company_id', $user->company_id);
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Project')
                ->columnSpanFull()
                ->tabs([
                    Schemas\Components\Tabs\Tab::make('Details')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\TextInput::make('code')->label('Project Code'),
                            Forms\Components\Select::make('project_type')
                                ->label('Project Type')
                                ->options(CdeProject::$projectTypes)
                                ->searchable()
                                ->reactive()
                                ->placeholder('Select type...'),
                            Forms\Components\Select::make('client_id')
                                ->relationship('client', 'name')->searchable()->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                    Forms\Components\TextInput::make('email')->email()->maxLength(255),
                                    Forms\Components\TextInput::make('phone')->maxLength(50),
                                    Forms\Components\TextInput::make('company_name')->label('Company')->maxLength(255),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    return \App\Models\Client::create(array_merge($data, [
                                        'company_id' => auth()->user()->company_id,
                                        'is_active'  => true,
                                    ]))->id;
                                }),
                            Forms\Components\Select::make('manager_id')
                                ->relationship('manager', 'name', fn($q) => $q->where('company_id', auth()->user()?->company_id)->where('is_active', true))
                                ->searchable()->preload()->label('Project Manager')
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                    Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                                    Forms\Components\TextInput::make('phone')->maxLength(50),
                                ])
                                ->createOptionUsing(function (array $data): int {
                                    return \App\Models\User::create(array_merge($data, [
                                        'company_id' => auth()->user()->company_id,
                                        'password'   => bcrypt(\Illuminate\Support\Str::random(16)),
                                    ]))->id;
                                }),
                            Forms\Components\Select::make('status')
                                ->options(CdeProject::$statuses)->default('planning'),
                            Forms\Components\Select::make('currency')
                                ->label('Project Currency')
                                ->options(
                                    collect(CdeProject::$currencies)
                                        ->mapWithKeys(fn($def, $code) => [$code => $def['symbol'] . ' — ' . $def['name'] . ' (' . ($def['position'] === 'before' ? $def['symbol'] . '100' : '100 ' . $def['symbol']) . ')'])
                                        ->toArray()
                                )
                                ->searchable()
                                ->placeholder('Inherit from company')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state && isset(CdeProject::$currencies[$state])) {
                                        $def = CdeProject::$currencies[$state];
                                        $set('currency_symbol', $def['symbol']);
                                        $set('currency_position', $def['position']);
                                    } else {
                                        $set('currency_symbol', null);
                                        $set('currency_position', 'before');
                                    }
                                })
                                ->helperText('Leave empty to use company default'),
                            Forms\Components\Hidden::make('currency_symbol'),
                            Forms\Components\Hidden::make('currency_position'),
                            Forms\Components\TextInput::make('budget')->numeric()
                                ->prefix(fn($get) => ($get('currency_position') ?? 'before') === 'before' ? ($get('currency_symbol') ?? CurrencyHelper::symbol()) : null)
                                ->suffix(fn($get) => ($get('currency_position') ?? 'before') === 'after' ? ($get('currency_symbol') ?? CurrencyHelper::symbol()) : null),
                            Forms\Components\DatePicker::make('start_date'),
                            Forms\Components\DatePicker::make('end_date'),
                        ])->columns(2),

                    Schemas\Components\Tabs\Tab::make('Location & Media')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\TextInput::make('address'),
                            Forms\Components\TextInput::make('city'),
                            Forms\Components\TextInput::make('country'),
                            Forms\Components\FileUpload::make('image')->image()
                                ->directory(fn($record) => $record ? StoragePath::images($record) : 'projects/images'),
                        ])->columns(2),

                    Schemas\Components\Tabs\Tab::make('Description')
                        ->icon('heroicon-o-pencil-square')
                        ->schema([
                            Forms\Components\RichEditor::make('description')->columnSpanFull(),
                        ]),

                    // ── Energy Project Tab (visible when type = energy) ──
                    Schemas\Components\Tabs\Tab::make('⚡ Energy')
                        ->icon('heroicon-o-bolt')
                        ->visible(fn($get) => $get('project_type') === 'energy')
                        ->schema([
                            Forms\Components\Select::make('energy_sector')
                                ->label('Energy Sector')
                                ->options(CdeProject::$energySectors)
                                ->searchable(),
                            Forms\Components\TextInput::make('capacity_mw')
                                ->label('Installed Capacity')
                                ->numeric()
                                ->suffix('MW'),
                            Forms\Components\TextInput::make('voltage_level')
                                ->label('Voltage Level')
                                ->placeholder('e.g. 33kV, 132kV, 400kV'),
                            Forms\Components\TextInput::make('grid_connection_point')
                                ->label('Grid Connection Point'),
                            Forms\Components\Select::make('commissioning_status')
                                ->label('Commissioning Status')
                                ->options(CdeProject::$commissioningStatuses),
                            Forms\Components\DatePicker::make('commercial_operation_date')
                                ->label('Commercial Operation Date (COD)'),
                            Forms\Components\TextInput::make('regulatory_license')
                                ->label('Regulatory License / ERA Permit'),
                        ])->columns(2),

                    // ── Road & Highway Tab (visible when type = road) ──
                    Schemas\Components\Tabs\Tab::make('🛣️ Road & Highway')
                        ->icon('heroicon-o-map')
                        ->visible(fn($get) => $get('project_type') === 'road')
                        ->schema([
                            Forms\Components\Select::make('road_class')
                                ->label('Road Classification')
                                ->options(CdeProject::$roadClasses)
                                ->searchable(),
                            Forms\Components\TextInput::make('road_length_km')
                                ->label('Total Road Length')
                                ->numeric()
                                ->suffix('km'),
                            Forms\Components\TextInput::make('road_width_m')
                                ->label('Carriageway Width')
                                ->numeric()
                                ->suffix('m'),
                            Forms\Components\TextInput::make('number_of_lanes')
                                ->label('Number of Lanes')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(8),
                            Forms\Components\Select::make('pavement_type')
                                ->label('Pavement Type')
                                ->options(CdeProject::$pavementTypes)
                                ->searchable(),
                            Forms\Components\TextInput::make('design_speed_kph')
                                ->label('Design Speed')
                                ->suffix('km/h'),
                            Forms\Components\TextInput::make('chainage_start')
                                ->label('Start Chainage')
                                ->placeholder('e.g. 0+000'),
                            Forms\Components\TextInput::make('chainage_end')
                                ->label('End Chainage')
                                ->placeholder('e.g. 45+350'),
                            Forms\Components\Select::make('terrain')
                                ->label('Terrain')
                                ->options(CdeProject::$terrainTypes),
                            Forms\Components\TextInput::make('funding_source')
                                ->label('Funding Source')
                                ->placeholder('e.g. World Bank, AfDB, GoU'),
                            Forms\Components\TextInput::make('road_authority')
                                ->label('Road Authority')
                                ->placeholder('e.g. UNRA, KCCA'),
                        ])->columns(2),

                    Schemas\Components\Tabs\Tab::make('Modules')
                        ->icon('heroicon-o-puzzle-piece')
                        ->badge(fn($record) => $record ? count($record->getEnabledModules()) : null)
                        ->schema([
                            Forms\Components\CheckboxList::make('modules')
                                ->label('Enabled Modules')
                                ->options(function () {
                                    $user = auth()->user();
                                    if (!$user || !$user->company_id) {
                                        return collect(Module::$availableModules)
                                            ->mapWithKeys(fn($def, $code) => [$code => $def['name'] . ' — ' . $def['description']]);
                                    }

                                    $companyModules = $user->company->getEnabledModules();

                                    return collect(Module::$availableModules)
                                        ->filter(fn($def, $code) => in_array($code, $companyModules))
                                        ->mapWithKeys(fn($def, $code) => [$code => $def['name'] . ' — ' . $def['description']]);
                                })
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Code')->searchable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->limit(UIStandards::LIMIT_TITLE),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->limit(UIStandards::LIMIT_NAME),
                Tables\Columns\TextColumn::make('manager.name')->label('PM')->limit(UIStandards::LIMIT_NAME),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => UIStandards::statusColor($state)),
                Tables\Columns\TextColumn::make('module_access_count')
                    ->label('Modules')
                    ->counts('moduleAccess')
                    ->badge()
                    ->color('primary')
                    ->suffix(' enabled'),
                Tables\Columns\TextColumn::make('budget')->formatStateUsing(CurrencyHelper::formatter()),
                Tables\Columns\TextColumn::make('start_date')->date(UIStandards::DATE_FORMAT),
                Tables\Columns\TextColumn::make('end_date')->date(UIStandards::DATE_FORMAT),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn(CdeProject $record) => static::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(CdeProject::$statuses),
            ])
            ->actions([Actions\ViewAction::make(), Actions\EditAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    /**
     * Build the sub-navigation that appears when viewing a project record.
     * Modules are grouped into logical categories using NavigationGroups.
     * Only shows modules that are enabled on this specific project.
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        $record = $page->getRecord();

        if (!$record instanceof CdeProject) {
            return [];
        }

        $enabledModules = $record->getEnabledModules();

        // Helper to build a NavigationItem for a module if enabled
        $makeItem = function (string $code, string $label, string $icon, string $pageClass) use ($page, $record, $enabledModules): ?NavigationItem {
            if (!in_array($code, $enabledModules)) {
                return null;
            }
            $routeName = self::moduleCodeToRouteName($code);
            return NavigationItem::make($label)
                ->icon($icon)
                ->isActiveWhen(fn() => $page instanceof $pageClass)
                ->url(static::getUrl($routeName, ['record' => $record]));
        };

        // ── Define grouped modules ──────────────────────────────
        $groups = [
            'Operations' => [
                'icon' => 'heroicon-o-wrench-screwdriver',
                'items' => [
                        // Unified MS‑Project schedule (merges old Tasks + Work Orders + Planning)
                    (in_array('task_workflow', $enabledModules) || in_array('core', $enabledModules) || in_array('planning_progress', $enabledModules))
                    ? NavigationItem::make('Schedule')
                        ->icon('heroicon-o-calendar-days')
                        ->isActiveWhen(fn() => $page instanceof Modules\TaskWorkflowPage)
                        ->url(static::getUrl('module-task-workflow', ['record' => $record]))
                    : null,
                ],
            ],
            'Site' => [
                'icon' => 'heroicon-o-map-pin',
                'items' => [
                    $makeItem('field_management', 'Field Logs', 'heroicon-o-map-pin', Modules\FieldManagementPage::class),
                    $makeItem('inventory', 'Inventory', 'heroicon-o-cube', Modules\InventoryPage::class),
                    $makeItem('sheq', 'SHEQ', 'heroicon-o-shield-check', Modules\SheqPage::class),
                ],
            ],
            'Resources' => [
                'icon' => 'heroicon-o-truck',
                'items' => [
                    $makeItem('equipment', 'Equipment', 'heroicon-o-truck', Modules\EquipmentPage::class),
                    $makeItem('subcontractors', 'Subcontractors', 'heroicon-o-user-group', Modules\SubcontractorPage::class),
                ],
            ],
            'Commercial' => [
                'icon' => 'heroicon-o-banknotes',
                'items' => [
                    $makeItem('financials', 'Financials', 'heroicon-o-banknotes', Modules\FinancialsPage::class),
                    $makeItem('cost_contracts', 'Contracts', 'heroicon-o-currency-dollar', Modules\CostContractsPage::class),
                    $makeItem('boq_management', 'BOQ', 'heroicon-o-calculator', Modules\BoqPage::class),
                ],
            ],
            'Information' => [
                'icon' => 'heroicon-o-document-text',
                'items' => [
                    $makeItem('cde', 'Documents', 'heroicon-o-folder-open', Modules\CdePage::class),
                    in_array('cde', $enabledModules)
                    ? NavigationItem::make('RFIs & Submittals')
                        ->icon('heroicon-o-question-mark-circle')
                        ->isActiveWhen(fn() => $page instanceof Modules\RfiSubmittalPage)
                        ->url(static::getUrl('module-rfi-submittals', ['record' => $record]))
                    : null,
                    $makeItem('reporting', 'Reports', 'heroicon-o-chart-bar', Modules\ReportingPage::class),
                    $makeItem('suggestion_box', 'Suggestions', 'heroicon-o-light-bulb', Modules\SuggestionBoxPage::class),
                ],
            ],
        ];

        // ── Build navigation array ──────────────────────────────
        $nav = [];

        // Always show the Overview tab first
        $nav[] = NavigationItem::make('Overview')
            ->icon('heroicon-o-home')
            ->isActiveWhen(fn() => $page instanceof Pages\ViewCdeProject)
            ->url(static::getUrl('view', ['record' => $record]))
            ->sort(1);

        $sort = 2;
        foreach ($groups as $groupLabel => $groupConfig) {
            $items = array_filter($groupConfig['items']); // Remove nulls (disabled modules)
            if (empty($items)) {
                continue;
            }

            // Single or multiple items → always wrap in NavigationGroup for consistency
            $nav[] = NavigationGroup::make($groupLabel)
                ->collapsible()
                ->items(array_values($items));
            $sort++;
        }

        // Always show Settings tab at the end
        $nav[] = NavigationItem::make('Settings')
            ->icon('heroicon-o-cog-6-tooth')
            ->isActiveWhen(fn() => $page instanceof Pages\EditCdeProject)
            ->url(static::getUrl('edit', ['record' => $record]))
            ->sort(99);

        return $nav;
    }

    /**
     * Convert a module code like 'task_workflow' to a route name like 'module-task-workflow'.
     */
    private static function moduleCodeToRouteName(string $code): string
    {
        return 'module-' . str_replace('_', '-', $code);
    }

    /**
     * Cache navigation badge data to avoid redundant queries.
     */
    private static function getNavigationBadgeData(): array
    {
        static $cache = null;
        if ($cache !== null)
            return $cache;

        $company = auth()->user()?->company;
        $count = (int) static::getEloquentQuery()->count();
        $limit = $company ? $company->getEffectiveMaxProjects() : 0;

        $cache = compact('company', 'count', 'limit');
        return $cache;
    }

    public static function getNavigationBadge(): ?string
    {
        $data = static::getNavigationBadgeData();

        if ($data['company'] && $data['limit']) {
            return "{$data['count']}/{$data['limit']}";
        }

        return (string) $data['count'];
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $data = static::getNavigationBadgeData();

        if (!$data['company'] || !$data['limit'])
            return 'primary';

        if ($data['count'] >= $data['limit'])
            return 'danger';
        if ($data['count'] >= $data['limit'] * 0.8)
            return 'warning';
        return 'primary';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCdeProjects::route('/'),
            'create' => Pages\CreateCdeProject::route('/create'),
            'view' => Pages\ViewCdeProject::route('/{record}'),
            'edit' => Pages\EditCdeProject::route('/{record}/edit'),

            // Module pages
            'module-core' => Modules\CoreFsmPage::route('/{record}/core-fsm'),
            'module-cde' => Modules\CdePage::route('/{record}/documents'),
            'module-rfi-submittals' => Modules\RfiSubmittalPage::route('/{record}/rfi-submittals'),
            'module-task-workflow' => Modules\TaskWorkflowPage::route('/{record}/tasks'),
            'module-field-management' => Modules\FieldManagementPage::route('/{record}/field-management'),
            'module-inventory' => Modules\InventoryPage::route('/{record}/inventory'),
            'module-financials' => Modules\FinancialsPage::route('/{record}/financials'),
            'module-cost-contracts' => Modules\CostContractsPage::route('/{record}/contracts'),
            'module-planning-progress' => Modules\PlanningProgressPage::route('/{record}/planning'),
            'module-boq-management' => Modules\BoqPage::route('/{record}/boq'),
            'module-sheq' => Modules\SheqPage::route('/{record}/sheq'),
            'module-reporting' => Modules\ReportingPage::route('/{record}/reports'),
            'module-equipment' => Modules\EquipmentPage::route('/{record}/equipment'),
            'module-subcontractors' => Modules\SubcontractorPage::route('/{record}/subcontractors'),
            'module-suggestion-box' => Modules\SuggestionBoxPage::route('/{record}/suggestions'),
        ];
    }
}
