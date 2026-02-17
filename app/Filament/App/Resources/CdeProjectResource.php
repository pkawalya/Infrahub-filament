<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CdeProjectResource\Pages;
use App\Filament\App\Resources\CdeProjectResource\Pages\Modules;
use App\Models\CdeProject;
use App\Models\Module;
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

class CdeProjectResource extends Resource
{
    protected static ?string $model = CdeProject::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Projects';
    protected static ?string $label = 'Project';
    protected static ?int $navigationSort = 1;
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Project Details')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('code')->label('Project Code'),
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')->searchable()->preload(),
                Forms\Components\Select::make('manager_id')
                    ->relationship('manager', 'name')->searchable()->preload()->label('Project Manager'),
                Forms\Components\Select::make('status')
                    ->options(CdeProject::$statuses)->default('planning'),
                Forms\Components\TextInput::make('budget')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix()),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
            ])->columns(2),

            Schemas\Components\Section::make('Location')->schema([
                Forms\Components\TextInput::make('address'),
                Forms\Components\TextInput::make('city'),
                Forms\Components\TextInput::make('country'),
                Forms\Components\FileUpload::make('image')->image()->directory('projects/images'),
            ])->columns(2)->collapsible(),

            Schemas\Components\Section::make('Project Modules')
                ->description('Select which modules to enable for this project. Only modules activated for your company are shown.')
                ->icon('heroicon-o-puzzle-piece')
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
                ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Code')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client'),
                Tables\Columns\TextColumn::make('manager.name')->label('PM'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'active' => 'success', 'planning' => 'info',
                        'on_hold' => 'warning', 'completed' => 'gray',
                        'cancelled' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('module_access_count')
                    ->label('Modules')
                    ->counts('moduleAccess')
                    ->badge()
                    ->color('primary')
                    ->suffix(' enabled'),
                Tables\Columns\TextColumn::make('budget')->formatStateUsing(CurrencyHelper::formatter()),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date(),
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
                    $makeItem('core', 'Work Orders', 'heroicon-o-wrench-screwdriver', Modules\CoreFsmPage::class),
                    $makeItem('task_workflow', 'Tasks', 'heroicon-o-clipboard-document-check', Modules\TaskWorkflowPage::class),
                    $makeItem('planning_progress', 'Planning', 'heroicon-o-calendar-days', Modules\PlanningProgressPage::class),
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
            'Commercial' => [
                'icon' => 'heroicon-o-banknotes',
                'items' => [
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

            // If only 1 item in group, show it directly (no group wrapper)
            if (count($items) === 1) {
                $item = reset($items);
                $nav[] = $item->sort($sort++);
                continue;
            }

            // Multiple items → wrap in NavigationGroup
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

    public static function getNavigationBadge(): ?string
    {
        $company = auth()->user()?->company;
        $count = (int) static::getEloquentQuery()->count();

        if ($company) {
            $limit = $company->getEffectiveMaxProjects();
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

        $limit = $company->getEffectiveMaxProjects();
        if (!$limit)
            return 'primary';

        $count = $company->projects()->count();
        if ($count >= $limit)
            return 'danger';
        if ($count >= $limit * 0.8)
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
            'module-cost-contracts' => Modules\CostContractsPage::route('/{record}/contracts'),
            'module-planning-progress' => Modules\PlanningProgressPage::route('/{record}/planning'),
            'module-boq-management' => Modules\BoqPage::route('/{record}/boq'),
            'module-sheq' => Modules\SheqPage::route('/{record}/sheq'),
            'module-reporting' => Modules\ReportingPage::route('/{record}/reports'),
        ];
    }
}
