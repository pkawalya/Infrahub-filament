<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CdeProjectResource\Pages;
use App\Filament\App\Resources\CdeProjectResource\Pages\Modules;
use App\Models\CdeProject;
use App\Models\Module;
use Filament\Actions;
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
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(CdeProject::$statuses),
            ])
            ->actions([Actions\ViewAction::make(), Actions\EditAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    /**
     * Build the sub-navigation that appears when viewing a project record.
     * Only shows module tabs for modules enabled on this specific project.
     */
    public static function getRecordSubNavigation(Page $page): array
    {
        $record = $page->getRecord();

        if (!$record instanceof CdeProject) {
            return [];
        }

        $enabledModules = $record->getEnabledModules();

        // Map of module_code => [page class, label, icon, sort]
        $modulePages = [
            'core' => ['class' => Modules\CoreFsmPage::class, 'label' => 'Core FSM', 'icon' => 'heroicon-o-wrench-screwdriver', 'sort' => 2],
            'cde' => ['class' => Modules\CdePage::class, 'label' => 'Documents', 'icon' => 'heroicon-o-folder-open', 'sort' => 3],
            'task_workflow' => ['class' => Modules\TaskWorkflowPage::class, 'label' => 'Tasks', 'icon' => 'heroicon-o-clipboard-document-check', 'sort' => 4],
            'field_management' => ['class' => Modules\FieldManagementPage::class, 'label' => 'Field Mgmt', 'icon' => 'heroicon-o-map-pin', 'sort' => 5],
            'inventory' => ['class' => Modules\InventoryPage::class, 'label' => 'Inventory', 'icon' => 'heroicon-o-cube', 'sort' => 6],
            'cost_contracts' => ['class' => Modules\CostContractsPage::class, 'label' => 'Contracts', 'icon' => 'heroicon-o-currency-dollar', 'sort' => 7],
            'planning_progress' => ['class' => Modules\PlanningProgressPage::class, 'label' => 'Planning', 'icon' => 'heroicon-o-calendar-days', 'sort' => 8],
            'boq_management' => ['class' => Modules\BoqPage::class, 'label' => 'BOQ', 'icon' => 'heroicon-o-calculator', 'sort' => 9],
            'sheq' => ['class' => Modules\SheqPage::class, 'label' => 'SHEQ', 'icon' => 'heroicon-o-shield-check', 'sort' => 10],
            'reporting' => ['class' => Modules\ReportingPage::class, 'label' => 'Reports', 'icon' => 'heroicon-o-chart-bar', 'sort' => 11],
        ];

        $nav = [];

        // Always show the Overview tab
        $nav[] = NavigationItem::make('Overview')
            ->icon('heroicon-o-home')
            ->isActiveWhen(fn() => $page instanceof Pages\ViewCdeProject)
            ->url(static::getUrl('view', ['record' => $record]))
            ->sort(1);

        // Add module tabs only for enabled modules
        foreach ($modulePages as $code => $config) {
            if (in_array($code, $enabledModules)) {
                $routeName = self::moduleCodeToRouteName($code);
                $nav[] = NavigationItem::make($config['label'])
                    ->icon($config['icon'])
                    ->isActiveWhen(fn() => $page instanceof $config['class'])
                    ->url(static::getUrl($routeName, ['record' => $record]))
                    ->sort($config['sort']);
            }
        }

        // Always show Edit tab at the end
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
