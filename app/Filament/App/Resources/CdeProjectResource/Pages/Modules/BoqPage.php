<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Boq;
use App\Models\BoqItem;
use App\Models\Contract;
use App\Models\User;
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

    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    public function getStats(): array
    {
        $pid = $this->pid();
        $base = Boq::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $approved = (clone $base)->where('status', 'approved')->count();
        $totalVal = (clone $base)->sum('total_value');
        $itemCount = BoqItem::whereHas('boq', fn($q) => $q->where('cde_project_id', $pid))->count();

        return [
            [
                'label' => 'Total BOQs',
                'value' => $total,
                'sub' => $approved . ' approved',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" /></svg>'
            ],
            [
                'label' => 'Total Value',
                'value' => CurrencyHelper::format($totalVal, 0),
                'sub' => 'All BOQs',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#eef2ff'
            ],
            [
                'label' => 'Line Items',
                'value' => $itemCount,
                'sub' => 'Across all BOQs',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
        ];
    }

    private function boqFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('BOQ Details')->schema([
                Forms\Components\TextInput::make('boq_number')->label('BOQ Number')
                    ->default(fn() => $isCreate ? 'BOQ-' . str_pad((string) (Boq::where('cde_project_id', $this->pid())->count() + 1), 4, '0', STR_PAD_LEFT) : null)
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('contract_id')->label('Linked Contract')
                    ->options(fn() => Contract::where('cde_project_id', $this->pid())->pluck('title', 'id'))
                    ->searchable()->nullable(),
                Forms\Components\Select::make('status')->options(Boq::$statuses)->required()->default($isCreate ? 'draft' : null),
                Forms\Components\TextInput::make('currency')->maxLength(3)->default('USD'),
            ])->columns(2),
            Section::make('Description')->schema([
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            ])->collapsed(!$isCreate),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBoq')
                ->label('New BOQ')->icon('heroicon-o-plus-circle')->color('primary')
                ->modalWidth('3xl')
                ->schema($this->boqFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['created_by'] = auth()->id();
                    $data['total_value'] = 0;
                    Boq::create($data);
                    Notification::make()->title('BOQ created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Boq::query()->where('cde_project_id', $this->pid())->with(['contract', 'items', 'creator']))
            ->columns([
                Tables\Columns\TextColumn::make('boq_number')->label('BOQ #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-calculator')->copyable(),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(40)->tooltip(fn(Boq $record) => $record->name),
                Tables\Columns\TextColumn::make('contract.title')->label('Contract')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'approved' => 'success', 'final' => 'primary', 'priced' => 'info', 'submitted' => 'warning', 'draft' => 'gray', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('items_count')->label('Items')->counts('items')->sortable(),
                Tables\Columns\TextColumn::make('total_value')->label('Total Value')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('currency')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Boq::$statuses)->multiple(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                \Filament\Actions\Action::make('manageItems')
                    ->label('Items')->icon('heroicon-o-list-bullet')->color('info')->modalWidth('5xl')
                    ->modalHeading(fn(Boq $record) => 'Line Items — ' . $record->boq_number . ' ' . $record->name)
                    ->schema([
                        Forms\Components\Repeater::make('items')->schema([
                            Forms\Components\TextInput::make('item_code')->required()->maxLength(20)->columnSpan(1),
                            Forms\Components\TextInput::make('description')->required()->columnSpan(3),
                            Forms\Components\TextInput::make('unit')->required()->maxLength(10)->columnSpan(1),
                            Forms\Components\TextInput::make('quantity')->numeric()->required()->default(0)->columnSpan(1),
                            Forms\Components\TextInput::make('unit_rate')->label('Rate')->numeric()->prefix('$')->required()->default(0)->columnSpan(1),
                            Forms\Components\TextInput::make('amount')->numeric()->prefix('$')->disabled()->dehydrated()->columnSpan(1),
                            Forms\Components\Select::make('category')->options([
                                'preliminaries' => 'Prelim',
                                'substructure' => 'Sub',
                                'superstructure' => 'Super',
                                'finishes' => 'Finish',
                                'services' => 'Serv',
                                'external_works' => 'External',
                                'other' => 'Other',
                            ])->columnSpan(1),
                        ])->columns(9)->addActionLabel('Add Line Item')->collapsible()->defaultItems(0)->reorderable()
                            ->itemLabel(fn(array $state): ?string => ($state['item_code'] ?? '') . ' ' . ($state['description'] ?? '') . ' — $' . number_format($state['amount'] ?? 0, 2)),
                    ])
                    ->fillForm(fn(Boq $record) => ['items' => $record->items()->orderBy('sort_order')->get()->toArray()])
                    ->action(function (array $data, Boq $record): void {
                        $record->items()->delete();
                        $order = 0;
                        $total = 0;
                        foreach ($data['items'] ?? [] as $item) {
                            $item['amount'] = round(($item['quantity'] ?? 0) * ($item['unit_rate'] ?? 0), 2);
                            $item['sort_order'] = $order++;
                            $total += $item['amount'];
                            $record->items()->create($item);
                        }
                        $record->update(['total_value' => $total]);
                        Notification::make()->title('Items updated — Total: ' . CurrencyHelper::format($total))->success()->send();
                    }),

                \Filament\Actions\Action::make('updateStatus')
                    ->label('Status')->icon('heroicon-o-arrow-path')->color('warning')
                    ->schema([Forms\Components\Select::make('status')->options(Boq::$statuses)->required()])
                    ->fillForm(fn(Boq $record) => ['status' => $record->status])
                    ->action(function (array $data, Boq $record): void {
                        $record->update($data);
                        Notification::make()->title('Status → ' . Boq::$statuses[$data['status']])->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')->modalWidth('3xl')
                    ->schema($this->boqFormSchema())
                    ->fillForm(fn(Boq $record) => $record->toArray())
                    ->action(function (array $data, Boq $record): void {
                        $record->update($data);
                        Notification::make()->title('BOQ updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')->color('gray')
                    ->requiresConfirmation()->modalDescription('Copies the BOQ with all line items.')
                    ->action(function (Boq $record): void {
                        $new = $record->replicate();
                        $new->boq_number = 'BOQ-' . str_pad((string) (Boq::where('cde_project_id', $this->pid())->count() + 1), 4, '0', STR_PAD_LEFT);
                        $new->status = 'draft';
                        $new->created_by = auth()->id();
                        $new->save();
                        foreach ($record->items as $item) {
                            $new->items()->create($item->only(['item_code', 'description', 'unit', 'quantity', 'unit_rate', 'amount', 'category', 'sort_order']));
                        }
                        Notification::make()->title('BOQ duplicated as ' . $new->boq_number)->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->action(fn(Boq $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkStatus')->label('Update Status')->icon('heroicon-o-arrow-path')
                        ->schema([Forms\Components\Select::make('status')->options(Boq::$statuses)->required()])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r)
                                $record->update($data);
                            Notification::make()->title($records->count() . ' BOQs updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Bills of Quantities')
            ->emptyStateDescription('Create BOQs to manage project costing.')
            ->emptyStateIcon('heroicon-o-calculator')
            ->striped()->paginated([10, 25, 50]);
    }
}
