<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Boq;
use App\Models\Contract;
use App\Support\CurrencyHelper;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class BoqPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'boq_management';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'BOQ';
    protected static ?string $title = 'Bill of Quantities';
    protected string $view = 'filament.app.pages.modules.boq';

    public function getStats(): array
    {
        $pid = $this->record->id;
        $total = Boq::where('cde_project_id', $pid)->count();
        $totalValue = Boq::where('cde_project_id', $pid)->sum('total_value');
        $totalItems = 0;
        $boqs = Boq::where('cde_project_id', $pid)->withCount('items')->get();
        foreach ($boqs as $b)
            $totalItems += $b->items_count;

        return [
            [
                'label' => 'BOQ Schedules',
                'value' => $total,
                'sub' => $totalItems . ' line items',
                'sub_type' => 'info',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" /></svg>'
            ],
            [
                'label' => 'Total Value',
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => 'All schedules',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Line Items',
                'value' => $totalItems,
                'sub' => 'Across all BOQs',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        $companyId = $this->record->company_id;
        $projectId = $this->record->id;

        return [
            Action::make('createBoq')
                ->label('New BOQ Schedule')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('2xl')
                ->schema([
                    Section::make('BOQ Schedule Details')->schema([
                        Forms\Components\TextInput::make('boq_number')->label('BOQ Number')
                            ->default(fn() => 'BOQ-' . str_pad((string) (Boq::where('cde_project_id', $projectId)->count() + 1), 3, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\TextInput::make('name')->required()->maxLength(255)->label('Schedule Name'),
                        Forms\Components\Select::make('contract_id')->label('Linked Contract')
                            ->options(Contract::where('cde_project_id', $projectId)->pluck('title', 'id'))->searchable()->nullable(),
                        Forms\Components\Select::make('status')->options([
                            'draft' => 'Draft',
                            'submitted' => 'Submitted',
                            'approved' => 'Approved',
                            'priced' => 'Priced',
                            'final' => 'Final',
                        ])->required()->default('draft'),
                        Forms\Components\Select::make('currency')->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'UGX' => 'UGX',
                            'KES' => 'KES',
                            'ZAR' => 'ZAR',
                        ])->default('USD'),
                        Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                    ])->columns(2),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    $data['created_by'] = auth()->id();
                    $data['total_value'] = 0;
                    Boq::create($data);
                    Notification::make()->title('BOQ Schedule created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(Boq::query()->where('cde_project_id', $projectId)->with(['contract', 'creator'])->withCount('items'))
            ->columns([
                Tables\Columns\TextColumn::make('boq_number')->label('BOQ #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-calculator'),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(45)->label('Schedule Name'),
                Tables\Columns\TextColumn::make('contract.title')->label('Contract')->placeholder('—')->limit(30),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(?string $state) => match ($state) { 'approved' => 'success', 'final' => 'primary', 'priced' => 'info', 'submitted' => 'warning', default => 'gray'}),
                Tables\Columns\TextColumn::make('items_count')->label('Items')->suffix(' items'),
                Tables\Columns\TextColumn::make('total_value')->formatStateUsing(CurrencyHelper::formatter())->label('Total Value'),
                Tables\Columns\TextColumn::make('currency')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'submitted' => 'Submitted',
                    'approved' => 'Approved',
                    'priced' => 'Priced',
                    'final' => 'Final',
                ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('manageItems')
                    ->label('Items')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->modalWidth('5xl')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('item_code')->required()->maxLength(20)->columnSpan(1),
                                Forms\Components\TextInput::make('description')->required()->columnSpan(3),
                                Forms\Components\TextInput::make('unit')->required()->maxLength(10)->columnSpan(1),
                                Forms\Components\TextInput::make('quantity')->numeric()->required()->default(0)->columnSpan(1),
                                Forms\Components\TextInput::make('unit_rate')->label('Rate')->numeric()->prefix('$')->required()->default(0)->columnSpan(1),
                                Forms\Components\TextInput::make('amount')->numeric()->prefix('$')->columnSpan(1),
                                Forms\Components\Select::make('category')->options([
                                    'preliminaries' => 'Prelim',
                                    'substructure' => 'Sub',
                                    'superstructure' => 'Super',
                                    'finishes' => 'Finish',
                                    'services' => 'Serv',
                                    'external_works' => 'External',
                                    'other' => 'Other',
                                ])->columnSpan(1),
                            ])
                            ->columns(9)
                            ->addActionLabel('Add Line Item')
                            ->collapsible()
                            ->defaultItems(0)
                            ->reorderable()
                            ->orderColumn('sort_order'),
                    ])
                    ->fillForm(fn(Boq $record) => ['items' => $record->items->toArray()])
                    ->action(function (array $data, Boq $record): void {
                        // Recalculate total
                        $total = $record->items()->sum('amount');
                        $record->update(['total_value' => $total]);
                        Notification::make()->title('BOQ items updated. Total: ' . CurrencyHelper::format($total))->success()->send();
                    }),

                \Filament\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('2xl')
                    ->schema([
                        Forms\Components\TextInput::make('boq_number')->disabled(),
                        Forms\Components\TextInput::make('name')->disabled(),
                        Forms\Components\TextInput::make('status')->disabled(),
                        Forms\Components\TextInput::make('total_value')->disabled()->prefix('$'),
                        Forms\Components\TextInput::make('currency')->disabled(),
                        Forms\Components\Textarea::make('description')->disabled()->rows(2)->columnSpanFull(),
                    ])
                    ->fillForm(fn(Boq $record) => $record->toArray())
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Forms\Components\TextInput::make('boq_number')->required(),
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\Select::make('contract_id')
                            ->options(fn() => Contract::where('cde_project_id', $this->record->id)->pluck('title', 'id'))->searchable()->nullable(),
                        Forms\Components\Select::make('status')->options([
                            'draft' => 'Draft',
                            'submitted' => 'Submitted',
                            'approved' => 'Approved',
                            'priced' => 'Priced',
                            'final' => 'Final',
                        ])->required(),
                        Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
                    ])
                    ->fillForm(fn(Boq $record) => $record->toArray())
                    ->action(function (array $data, Boq $record): void {
                        $record->update($data);
                        Notification::make()->title('BOQ updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Boq $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No BOQ Schedules')
            ->emptyStateDescription('Create BOQ schedules to manage quantities and costs.')
            ->emptyStateIcon('heroicon-o-calculator');
    }
}
