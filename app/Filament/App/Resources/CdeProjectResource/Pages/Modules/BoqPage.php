<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Boq;
use App\Models\BoqItem;
use App\Models\BoqRevision;
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

    /* ── Category Labels ── */
    public static array $categories = [
        'preliminaries' => 'Preliminaries & General',
        'substructure' => 'Substructure',
        'superstructure' => 'Superstructure',
        'finishes' => 'Finishes',
        'services' => 'M&E Services',
        'external_works' => 'External Works',
        'provisional' => 'Provisional Sums',
        'dayworks' => 'Dayworks',
        'other' => 'Other',
    ];

    /* ══════════════════ STATS ══════════════════ */

    public function getStats(): array
    {
        $pid = $this->pid();
        $base = Boq::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $approved = (clone $base)->where('status', 'approved')->count();
        $totalVal = (clone $base)->sum('total_value');
        $itemCount = BoqItem::whereHas('boq', fn($q) => $q->where('cde_project_id', $pid))->count();
        $variationCount = BoqItem::whereHas('boq', fn($q) => $q->where('cde_project_id', $pid))->where('is_variation', true)->count();

        // Overall progress
        $allItems = BoqItem::whereHas('boq', fn($q) => $q->where('cde_project_id', $pid));
        $totalQty = (clone $allItems)->sum('quantity');
        $completedQty = (clone $allItems)->sum('quantity_completed');
        $progressPct = $totalQty > 0 ? round(($completedQty / $totalQty) * 100) : 0;

        return [
            [
                'label' => 'Total BOQs',
                'value' => $total,
                'sub' => $approved . ' approved',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zm0 2.25h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V18zm2.504-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zm0 2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V18zm2.498-6.75h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" /></svg>',
            ],
            [
                'label' => 'Total Value',
                'value' => CurrencyHelper::format($totalVal, 0),
                'sub' => 'All BOQs combined',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#eef2ff',
            ],
            [
                'label' => 'Overall Progress',
                'value' => $progressPct . '%',
                'sub' => $itemCount . ' line items · ' . $variationCount . ' variations',
                'sub_type' => $progressPct >= 75 ? 'success' : ($progressPct >= 40 ? 'info' : 'neutral'),
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>',
                'icon_bg' => '#ecfdf5',
            ],
            [
                'label' => 'Revisions',
                'value' => BoqRevision::whereHas('boq', fn($q) => $q->where('cde_project_id', $pid))->count(),
                'sub' => 'Tracked snapshots',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#eff6ff',
            ],
        ];
    }

    /* ══════════════════ CATEGORY SUMMARY (for blade) ══════════════════ */

    public function getCategorySummary(): array
    {
        $pid = $this->pid();
        $items = BoqItem::whereHas('boq', fn($q) => $q->where('cde_project_id', $pid))->get();
        if ($items->isEmpty())
            return [];

        $byCategory = $items->groupBy('category');
        $summary = [];
        foreach ($byCategory as $cat => $catItems) {
            $catTotal = $catItems->sum('amount');
            $catQty = $catItems->sum('quantity');
            $catCompleted = $catItems->sum('quantity_completed');
            $pct = $catQty > 0 ? round(($catCompleted / $catQty) * 100) : 0;
            $variationAmt = $catItems->where('is_variation', true)->sum('amount');
            $summary[] = [
                'key' => $cat ?: 'uncategorized',
                'label' => self::$categories[$cat] ?? ucfirst(str_replace('_', ' ', $cat ?: 'Uncategorized')),
                'total' => $catTotal,
                'total_formatted' => CurrencyHelper::format($catTotal, 0),
                'count' => $catItems->count(),
                'progress' => $pct,
                'variations' => $variationAmt,
            ];
        }
        usort($summary, fn($a, $b) => $b['total'] <=> $a['total']);
        return $summary;
    }

    /* ══════════════════ FORM SCHEMAS ══════════════════ */

    private function boqFormSchema(bool $isCreate = false): array
    {
        return [
            Forms\Components\TextInput::make('boq_number')->label('BOQ #')
                ->default(fn() => $isCreate ? 'BOQ-' . str_pad((string) (Boq::where('cde_project_id', $this->pid())->count() + 1), 4, '0', STR_PAD_LEFT) : null)
                ->required()->maxLength(50),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Select::make('contract_id')->label('Contract')
                ->options(fn() => Contract::where('cde_project_id', $this->pid())->pluck('title', 'id'))
                ->searchable()->nullable(),
            Forms\Components\Select::make('status')->options(Boq::$statuses)->required()->default($isCreate ? 'draft' : null),
            Forms\Components\TextInput::make('currency')->maxLength(3)->default('USD'),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
        ];
    }



    /* ══════════════════ HEADER ACTIONS ══════════════════ */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBoq')
                ->label('New BOQ')->icon('heroicon-o-plus-circle')->color('primary')
                ->modalWidth('lg')
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

    /* ══════════════════ VIEW DETAIL HELPERS ══════════════════ */

    private function viewDetailSchema(Boq $record): array
    {
        $items = $record->items()->orderBy('category')->orderBy('sort_order')->get();
        $byCategory = $items->groupBy('category');

        $schema = [
            Section::make('BOQ Information')->schema([
                Forms\Components\TextInput::make('boq_number')->label('BOQ #')->disabled(),
                Forms\Components\TextInput::make('status_label')->label('Status')->disabled(),
                Forms\Components\TextInput::make('contract_name')->label('Contract')->disabled(),
                Forms\Components\TextInput::make('currency')->disabled(),
                Forms\Components\TextInput::make('created_by_name')->label('Created By')->disabled(),
                Forms\Components\TextInput::make('created_display')->label('Created')->disabled(),
            ])->columns(3),
        ];

        // Category sections with item tables
        foreach ($byCategory as $cat => $catItems) {
            $catLabel = self::$categories[$cat] ?? ucfirst(str_replace('_', ' ', $cat ?: 'Uncategorized'));
            $catTotal = $catItems->sum('amount');
            $catQty = $catItems->sum('quantity');
            $catCompleted = $catItems->sum('quantity_completed');
            $pct = $catQty > 0 ? round(($catCompleted / $catQty) * 100) : 0;

            $lines = $catItems->map(
                fn($i) =>
                ($i->is_variation ? '🔸 ' : '') .
                $i->item_code . '  |  ' . $i->description .
                '  |  ' . number_format((float) $i->quantity, 2) . ' ' . $i->unit .
                '  ×  $' . number_format((float) $i->unit_rate, 2) .
                '  =  $' . number_format((float) $i->amount, 2) .
                ($i->quantity_completed > 0 ? '  (' . round(($i->quantity_completed / max($i->quantity, 0.01)) * 100) . '% done)' : '')
            );

            $schema[] = Section::make("{$catLabel} — " . CurrencyHelper::format($catTotal) . " ({$pct}% progress)")
                ->schema([
                    Forms\Components\Placeholder::make('cat_' . ($cat ?: 'uncategorized'))
                        ->content($lines->join("\n"))
                        ->columnSpanFull(),
                ]);
        }

        // Grand total
        $originalTotal = $items->where('is_variation', false)->sum('amount');
        $variationTotal = $items->where('is_variation', true)->sum('amount');
        $schema[] = Section::make('Grand Total: ' . CurrencyHelper::format($record->total_value))->schema([
            Forms\Components\Placeholder::make('grand_summary')
                ->content(
                    "Original Value: " . CurrencyHelper::format($originalTotal) . "\n" .
                    "Variations: " . CurrencyHelper::format($variationTotal) . " (" . $items->where('is_variation', true)->count() . " items)\n" .
                    "Final Value: " . CurrencyHelper::format($record->total_value) . "\n" .
                    "Items: " . $items->count() . "  |  Categories: " . $byCategory->count()
                )->columnSpanFull(),
        ]);

        // Notes / Description
        if ($record->description || $record->notes) {
            $schema[] = Section::make('Notes')->schema([
                Forms\Components\Textarea::make('description')->disabled()->rows(2)->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->label('Internal Notes')->disabled()->rows(2)->columnSpanFull(),
            ])->collapsed();
        }

        return $schema;
    }

    private function viewDetailData(Boq $record): array
    {
        $record->load(['contract', 'creator']);
        return [
            'boq_number' => $record->boq_number,
            'status_label' => Boq::$statuses[$record->status] ?? $record->status,
            'contract_name' => $record->contract?->title ?? '—',
            'currency' => $record->currency,
            'created_by_name' => $record->creator?->name ?? '—',
            'created_display' => $record->created_at?->format('M d, Y'),
            'description' => $record->description ?? '',
            'notes' => $record->notes ?? '',
        ];
    }

    /* ══════════════════ TABLE ══════════════════ */

    public function table(Table $table): Table
    {
        return $table
            ->query(Boq::query()->where('cde_project_id', $this->pid())->with(['contract', 'items', 'creator', 'revisions']))
            ->columns([
                Tables\Columns\TextColumn::make('boq_number')->label('BOQ #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-calculator')->copyable(),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(35)->tooltip(fn(Boq $record) => $record->name),
                Tables\Columns\TextColumn::make('contract.title')->label('Contract')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'approved' => 'success', 'final' => 'primary', 'priced' => 'info', 'submitted' => 'warning', 'draft' => 'gray', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('items_count')->label('Items')->counts('items')->sortable(),
                Tables\Columns\TextColumn::make('total_value')->label('Total Value')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->state(function (Boq $record) {
                        $totalQty = $record->items->sum('quantity');
                        $completedQty = $record->items->sum('quantity_completed');
                        return $totalQty > 0 ? round(($completedQty / $totalQty) * 100) . '%' : '—';
                    })
                    ->color(function (Boq $record) {
                        $totalQty = $record->items->sum('quantity');
                        if ($totalQty == 0)
                            return null;
                        $pct = ($record->items->sum('quantity_completed') / $totalQty) * 100;
                        return $pct >= 100 ? 'success' : ($pct >= 50 ? 'info' : null);
                    }),
                Tables\Columns\TextColumn::make('revisions_count')->label('Rev.')->counts('revisions')->sortable()->toggleable(),
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

                    /* ── View Detail ── */
                    \Filament\Actions\Action::make('viewDetail')
                        ->label('View')->icon('heroicon-o-eye')->color('gray')->modalWidth('screen')
                        ->modalHeading(fn(Boq $record) => $record->boq_number . ' — ' . $record->name)
                        ->schema(fn(Boq $record) => $this->viewDetailSchema($record))
                        ->fillForm(fn(Boq $record) => $this->viewDetailData($record))
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    /* ── Add Line Item ── */
                    \Filament\Actions\Action::make('addItem')
                        ->label('Add Item')->icon('heroicon-o-plus-circle')->color('info')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => 'Add Items — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->label('Line Items to Add')
                                ->addActionLabel('Add Another Item')
                                ->defaultItems(1)
                                ->schema([
                                    \Filament\Schemas\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('item_code')->required()->maxLength(20),
                                        Forms\Components\TextInput::make('description')->required()->columnSpan(2),
                                        Forms\Components\TextInput::make('unit')->required()->maxLength(10),
                                        Forms\Components\TextInput::make('quantity')->numeric()->required()->default(0),
                                        Forms\Components\TextInput::make('unit_rate')->label('Rate')->numeric()->prefix('$')->required()->default(0),
                                        Forms\Components\Select::make('category')->options(self::$categories)->searchable(),
                                        Forms\Components\Textarea::make('remarks')->rows(1)->columnSpan(2)->placeholder('Notes...'),
                                    ]),
                                ])->collapsible()
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $sortOrder = ($record->items()->max('sort_order') ?? 0);
                            $count = 0;
                            $totalAdded = 0;
                            foreach ($data['items'] ?? [] as $item) {
                                $item['amount'] = round(($item['quantity'] ?? 0) * ($item['unit_rate'] ?? 0), 2);
                                $item['sort_order'] = ++$sortOrder;
                                $record->items()->create($item);
                                $count++;
                                $totalAdded += $item['amount'];
                            }
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title("{$count} items added — ($" . number_format($totalAdded, 2) . ")")->success()->send();
                        }),

                    /* ── Edit Item ── */
                    \Filament\Actions\Action::make('editItem')
                        ->label('Edit Item')->icon('heroicon-o-pencil-square')->color('info')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => 'Edit Item — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Select::make('item_id')->label('Select Item')
                                ->options(fn(Boq $record) => $record->items->mapWithKeys(fn($i) =>
                                    [$i->id => $i->item_code . ' — ' . \Illuminate\Support\Str::limit($i->description, 50) . ' ($' . number_format((float) $i->amount, 2) . ')']))
                                ->required()->searchable()->live()
                                ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                    if ($item = BoqItem::find($state)) {
                                        $set('item_code', $item->item_code);
                                        $set('description', $item->description);
                                        $set('unit', $item->unit);
                                        $set('quantity', $item->quantity);
                                        $set('unit_rate', $item->unit_rate);
                                        $set('category', $item->category);
                                        $set('remarks', $item->remarks);
                                    }
                                }),
                            \Filament\Schemas\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('item_code')->required()->maxLength(20),
                                Forms\Components\TextInput::make('description')->required()->columnSpan(2),
                                Forms\Components\TextInput::make('unit')->required()->maxLength(10),
                                Forms\Components\TextInput::make('quantity')->numeric()->required(),
                                Forms\Components\TextInput::make('unit_rate')->label('Rate')->numeric()->prefix('$')->required(),
                                Forms\Components\Select::make('category')->options(self::$categories)->searchable(),
                                Forms\Components\Textarea::make('remarks')->rows(1)->columnSpan(2)->placeholder('Notes...'),
                            ]),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $item = BoqItem::where('id', $data['item_id'])->where('boq_id', $record->id)->first();
                            if (!$item)
                                return;
                            unset($data['item_id']);
                            $data['amount'] = round(($data['quantity'] ?? 0) * ($data['unit_rate'] ?? 0), 2);
                            $item->update($data);
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title('Item updated — ' . $data['item_code'])->success()->send();
                        }),

                    /* ── Delete Items ── */
                    \Filament\Actions\Action::make('deleteItems')
                        ->label('Delete Items')->icon('heroicon-o-trash')->color('danger')
                        ->modalHeading(fn(Boq $record) => 'Delete Items — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\CheckboxList::make('item_ids')
                                ->label('Select items to remove')
                                ->options(fn(Boq $record) => $record->items->mapWithKeys(fn($i) =>
                                    [$i->id => ($i->is_variation ? '🔸 ' : '') . $i->item_code . ' — ' . \Illuminate\Support\Str::limit($i->description, 40) . ' ($' . number_format((float) $i->amount, 2) . ')']))
                                ->required()->searchable()->columns(1),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $count = count($data['item_ids']);
                            BoqItem::whereIn('id', $data['item_ids'])->where('boq_id', $record->id)->delete();
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title("{$count} items deleted")->danger()->send();
                        }),

                    /* ── Import CSV ── */
                    \Filament\Actions\Action::make('importItems')
                        ->label('Import CSV')->icon('heroicon-o-arrow-up-tray')->color('gray')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => 'Import Items — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Textarea::make('csv_data')
                                ->label('Paste CSV Data (one item per line)')
                                ->helperText('Format: item_code, description, unit, quantity, unit_rate, category')
                                ->placeholder("A001, Concrete Grade 25, m³, 150, 120.00, substructure\nA002, Reinforcement Y16, kg, 5000, 1.85, superstructure")
                                ->rows(12)->required()->columnSpanFull(),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $lines = array_filter(array_map('trim', explode("\n", $data['csv_data'])));
                            $order = ($record->items()->max('sort_order') ?? 0);
                            $count = 0;
                            foreach ($lines as $line) {
                                $cols = str_getcsv(trim($line));
                                if (count($cols) < 5)
                                    continue;
                                $qty = floatval($cols[3]);
                                $rate = floatval($cols[4]);
                                $record->items()->create([
                                    'item_code' => trim($cols[0]),
                                    'description' => trim($cols[1]),
                                    'unit' => trim($cols[2]),
                                    'quantity' => $qty,
                                    'unit_rate' => $rate,
                                    'amount' => round($qty * $rate, 2),
                                    'category' => isset($cols[5]) ? trim($cols[5]) : null,
                                    'sort_order' => ++$order,
                                ]);
                                $count++;
                            }
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title("{$count} items imported — Total: " . CurrencyHelper::format($record->fresh()->total_value))->success()->send();
                        }),

                    /* ── Track Progress (per item) ── */
                    \Filament\Actions\Action::make('trackProgress')
                        ->label('Progress')->icon('heroicon-o-chart-bar')->color('success')
                        ->modalHeading(fn(Boq $record) => 'Update Progress — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Select::make('item_id')->label('Select Item')
                                ->options(fn(Boq $record) => $record->items->mapWithKeys(fn($i) => [
                                    $i->id => $i->item_code . ' — ' . \Illuminate\Support\Str::limit($i->description, 40) .
                                        ' (' . number_format((float) $i->quantity_completed, 1) . '/' . number_format((float) $i->quantity, 1) . ' ' . $i->unit . ')',
                                ]))
                                ->required()->searchable()->live()
                                ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                    if ($item = BoqItem::find($state)) {
                                        $set('total_qty', number_format((float) $item->quantity, 2) . ' ' . $item->unit);
                                        $set('quantity_completed', $item->quantity_completed);
                                    }
                                }),
                            Forms\Components\TextInput::make('total_qty')->label('Total Quantity')->disabled(),
                            Forms\Components\TextInput::make('quantity_completed')->label('Quantity Completed')->numeric()->required(),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            BoqItem::where('id', $data['item_id'])->where('boq_id', $record->id)
                                ->update(['quantity_completed' => $data['quantity_completed'] ?? 0]);
                            $items = $record->items()->get();
                            $totalQty = $items->sum('quantity');
                            $completedQty = $items->sum('quantity_completed');
                            $pct = $totalQty > 0 ? round(($completedQty / $totalQty) * 100) : 0;
                            Notification::make()->title("Progress updated — {$pct}% overall")->success()->send();
                        }),

                    /* ── Add Variation ── */
                    \Filament\Actions\Action::make('addVariation')
                        ->label('Variation')->icon('heroicon-o-plus')->color('warning')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => 'Add Variations — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->label('Variations to Add')
                                ->addActionLabel('Add Another Variation')
                                ->defaultItems(1)
                                ->schema([
                                    \Filament\Schemas\Components\Grid::make(3)->schema([
                                        Forms\Components\TextInput::make('item_code')->required()->maxLength(20),
                                        Forms\Components\TextInput::make('description')->required()->columnSpan(2),
                                        Forms\Components\TextInput::make('unit')->required()->maxLength(10),
                                        Forms\Components\TextInput::make('quantity')->numeric()->required()->default(0),
                                        Forms\Components\TextInput::make('unit_rate')->label('Rate')->numeric()->prefix('$')->required()->default(0),
                                        Forms\Components\Select::make('category')->options(self::$categories)->searchable(),
                                        Forms\Components\Textarea::make('remarks')->rows(1)->columnSpan(2)->placeholder('Reason for variation...'),
                                    ]),
                                ])->collapsible()
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $sortOrder = ($record->items()->max('sort_order') ?? 0);
                            $count = 0;
                            $totalAdded = 0;
                            foreach ($data['items'] ?? [] as $item) {
                                $item['amount'] = round(($item['quantity'] ?? 0) * ($item['unit_rate'] ?? 0), 2);
                                $item['sort_order'] = ++$sortOrder;
                                $item['is_variation'] = true;
                                $record->items()->create($item);
                                $count++;
                                $totalAdded += $item['amount'];
                            }
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title("{$count} variations added — ($" . number_format($totalAdded, 2) . ")")->success()->send();
                        }),

                    /* ── Create Revision Snapshot ── */
                    \Filament\Actions\Action::make('createRevision')
                        ->label('Revision')->icon('heroicon-o-clock')->color('info')
                        ->modalHeading(fn(Boq $record) => 'Create Revision — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\TextInput::make('revision_number')->label('Revision #')->required()->maxLength(20)
                                ->default(fn(Boq $record) => 'Rev.' . ($record->revisions()->count() + 1)),
                            Forms\Components\Textarea::make('change_description')->label('What Changed?')->rows(3)->required(),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            BoqRevision::create([
                                'boq_id' => $record->id,
                                'revision_number' => $data['revision_number'],
                                'change_description' => $data['change_description'],
                                'snapshot' => [
                                    'total_value' => $record->total_value,
                                    'status' => $record->status,
                                    'items_count' => $record->items()->count(),
                                    'items' => $record->items()->orderBy('sort_order')->get()->toArray(),
                                    'created_at' => now()->toIso8601String(),
                                ],
                                'created_by' => auth()->id(),
                            ]);
                            Notification::make()->title('Revision ' . $data['revision_number'] . ' saved')->success()->send();
                        }),

                    /* ── View Revisions ── */
                    \Filament\Actions\Action::make('viewRevisions')
                        ->label('History')->icon('heroicon-o-clock')->color('gray')
                        ->modalWidth('screen')
                        ->modalHeading(fn(Boq $record) => 'Revision History — ' . $record->boq_number)
                        ->schema(function (Boq $record) {
                            $revisions = $record->revisions()->with('creator')->orderByDesc('created_at')->get();
                            if ($revisions->isEmpty()) {
                                return [Forms\Components\Placeholder::make('no_revisions')->content('No revisions yet. Create one to track changes.')];
                            }
                            return $revisions->map(
                                fn(BoqRevision $rev) =>
                                Forms\Components\Placeholder::make('rev_' . $rev->id)
                                    ->label($rev->revision_number . ' — ' . ($rev->creator?->name ?? 'Unknown') . ' · ' . $rev->created_at->format('M d, Y H:i'))
                                    ->content(
                                        $rev->change_description . "\n" .
                                        'Value: ' . CurrencyHelper::format($rev->snapshot['total_value'] ?? 0) .
                                        ' | Items: ' . ($rev->snapshot['items_count'] ?? count($rev->snapshot['items'] ?? []))
                                    )
                            )->toArray();
                        })
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    /* ── Approve BOQ ── */
                    \Filament\Actions\Action::make('approve')
                        ->label('Approve')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(Boq $record) => !in_array($record->status, ['approved', 'final']))
                        ->requiresConfirmation()
                        ->modalDescription('Mark this BOQ as approved. A revision snapshot will be created automatically.')
                        ->action(function (Boq $record): void {
                            // Auto-create revision snapshot before approval
                            BoqRevision::create([
                                'boq_id' => $record->id,
                                'revision_number' => 'Approval',
                                'change_description' => 'BOQ approved by ' . auth()->user()->name,
                                'snapshot' => [
                                    'total_value' => $record->total_value,
                                    'status' => $record->status,
                                    'items_count' => $record->items()->count(),
                                    'items' => $record->items()->orderBy('sort_order')->get()->toArray(),
                                    'created_at' => now()->toIso8601String(),
                                ],
                                'created_by' => auth()->id(),
                            ]);
                            $record->update([
                                'status' => 'approved',
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);
                            Notification::make()->title('BOQ approved ✓')->success()->send();
                        }),

                    /* ── Update Status ── */
                    \Filament\Actions\Action::make('updateStatus')
                        ->label('Status')->icon('heroicon-o-arrow-path')->color('warning')
                        ->schema([Forms\Components\Select::make('status')->options(Boq::$statuses)->required()])
                        ->fillForm(fn(Boq $record) => ['status' => $record->status])
                        ->action(function (array $data, Boq $record): void {
                            $record->update($data);
                            Notification::make()->title('Status → ' . Boq::$statuses[$data['status']])->success()->send();
                        }),

                    /* ── Edit ── */
                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('lg')
                        ->schema($this->boqFormSchema())
                        ->fillForm(fn(Boq $record) => $record->toArray())
                        ->action(function (array $data, Boq $record): void {
                            $record->update($data);
                            Notification::make()->title('BOQ updated')->success()->send();
                        }),

                    /* ── Duplicate ── */
                    \Filament\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')->color('gray')
                        ->requiresConfirmation()->modalDescription('Copies the BOQ with all line items (excluding variations).')
                        ->action(function (Boq $record): void {
                            $new = $record->replicate(['approved_by', 'approved_at']);
                            $new->boq_number = 'BOQ-' . str_pad((string) (Boq::where('cde_project_id', $this->pid())->count() + 1), 4, '0', STR_PAD_LEFT);
                            $new->status = 'draft';
                            $new->created_by = auth()->id();
                            $new->save();
                            foreach ($record->items->where('is_variation', false) as $item) {
                                $clone = $item->only(['item_code', 'description', 'unit', 'quantity', 'unit_rate', 'amount', 'category', 'sort_order', 'remarks']);
                                $clone['quantity_completed'] = 0;
                                $new->items()->create($clone);
                            }
                            $new->update(['total_value' => $new->items()->sum('amount')]);
                            Notification::make()->title('BOQ duplicated as ' . $new->boq_number)->success()->send();
                        }),

                    /* ── Delete ── */
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
                                $r->update($data);
                            Notification::make()->title($records->count() . ' BOQs updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Bills of Quantities')
            ->emptyStateDescription('Create BOQs to manage project costing, track progress, and handle variations.')
            ->emptyStateIcon('heroicon-o-calculator')
            ->striped()->paginated([10, 25, 50]);
    }
}
