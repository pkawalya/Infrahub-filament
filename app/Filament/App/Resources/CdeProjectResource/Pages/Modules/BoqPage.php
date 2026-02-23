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
use Illuminate\Support\HtmlString;

use App\Filament\App\Concerns\ExportsTableCsv;

class BoqPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, ExportsTableCsv;

    protected static string $moduleCode = 'boq_management';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'BOQ';
    protected static ?string $title = 'Bill of Quantities';
    protected string $view = 'filament.app.pages.modules.boq';

    // Active sub-tab for viewing items within a BOQ
    public ?int $expandedBoqId = null;

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

    /* ══════════════════ CATEGORY SUMMARY ══════════════════ */

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

    /* ══════════════════ HELPER: Get items for a BOQ (for blade) ══════════════════ */

    public function getBoqItems(int $boqId): array
    {
        $boq = Boq::where('id', $boqId)->where('cde_project_id', $this->pid())->first();
        if (!$boq)
            return [];

        return $boq->items()->orderBy('category')->orderBy('sort_order')->get()
            ->map(fn(BoqItem $item) => [
                'id' => $item->id,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'unit' => $item->unit,
                'quantity' => number_format((float) $item->quantity, 2),
                'quantity_completed' => number_format((float) $item->quantity_completed, 2),
                'unit_rate' => CurrencyHelper::format($item->unit_rate),
                'amount' => CurrencyHelper::format($item->amount),
                'amount_raw' => (float) $item->amount,
                'category' => self::$categories[$item->category] ?? ucfirst($item->category ?? 'Other'),
                'category_key' => $item->category,
                'is_variation' => $item->is_variation,
                'progress_pct' => $item->quantity > 0 ? round(($item->quantity_completed / $item->quantity) * 100) : 0,
                'remarks' => $item->remarks,
            ])->toArray();
    }

    public function toggleBoqExpand(int $boqId): void
    {
        $this->expandedBoqId = $this->expandedBoqId === $boqId ? null : $boqId;
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

    /* ══════════════════ VIEW DETAIL ══════════════════ */

    private function viewDetailSchema(Boq $record): array
    {
        $items = $record->items()->orderBy('category')->orderBy('sort_order')->get();
        $byCategory = $items->groupBy('category');
        $record->load(['contract', 'creator']);

        // ── Compact info bar instead of full Section with form fields ──
        $infoCells = collect([
            ['BOQ', $record->boq_number],
            ['Status', Boq::$statuses[$record->status] ?? $record->status],
            ['Contract', $record->contract?->title ?? '—'],
            ['Currency', $record->currency],
            ['By', $record->creator?->name ?? '—'],
            ['Created', $record->created_at?->format('M d, Y')],
        ])->map(
                fn($c) =>
                '<div style="display:flex;gap:3px;align-items:baseline;">' .
                '<span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:#9ca3af;">' . $c[0] . '</span>' .
                '<span style="font-size:11px;font-weight:600;color:#1f2937;">' . e($c[1]) . '</span></div>'
            )->join('');

        $schema = [
            Forms\Components\Placeholder::make('info_bar')
                ->content(fn() => new HtmlString(
                    '<div style="display:flex;flex-wrap:wrap;gap:8px 16px;padding:6px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;">' .
                    $infoCells . '</div>'
                ))->columnSpanFull(),
        ];

        // ── Category item tables — compact ──
        foreach ($byCategory as $cat => $catItems) {
            $catLabel = self::$categories[$cat] ?? ucfirst(str_replace('_', ' ', $cat ?: 'Uncategorized'));
            $catTotal = $catItems->sum('amount');
            $catQty = $catItems->sum('quantity');
            $catCompleted = $catItems->sum('quantity_completed');
            $pct = $catQty > 0 ? round(($catCompleted / $catQty) * 100) : 0;

            $tableHtml = '<table style="width:100%;border-collapse:collapse;font-size:10px;">' .
                '<thead><tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">' .
                '<th style="text-align:left;padding:2px 4px;font-weight:700;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#64748b;">Code</th>' .
                '<th style="text-align:left;padding:2px 4px;font-weight:700;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#64748b;">Description</th>' .
                '<th style="text-align:center;padding:2px 4px;font-weight:700;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#64748b;">Unit</th>' .
                '<th style="text-align:right;padding:2px 4px;font-weight:700;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#64748b;">Qty</th>' .
                '<th style="text-align:right;padding:2px 4px;font-weight:700;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#64748b;">Rate</th>' .
                '<th style="text-align:right;padding:2px 4px;font-weight:700;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#64748b;">Amount</th>' .
                '<th style="text-align:center;padding:2px 4px;font-weight:700;font-size:8px;text-transform:uppercase;letter-spacing:0.3px;color:#64748b;">%</th>' .
                '</tr></thead><tbody>';

            foreach ($catItems as $i) {
                $itemPct = $i->quantity > 0 ? round(($i->quantity_completed / $i->quantity) * 100) : 0;
                $pctColor = $itemPct >= 100 ? '#059669' : ($itemPct >= 50 ? '#2563eb' : '#94a3b8');
                $tableHtml .= '<tr style="border-bottom:1px solid #f1f5f9;">' .
                    '<td style="padding:2px 4px;font-family:monospace;font-size:9px;white-space:nowrap;">' . ($i->is_variation ? '🔸' : '') . e($i->item_code) . '</td>' .
                    '<td style="padding:2px 4px;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="' . e($i->description) . '">' . e(\Illuminate\Support\Str::limit($i->description, 50)) . '</td>' .
                    '<td style="text-align:center;padding:2px 4px;font-size:9px;">' . e($i->unit) . '</td>' .
                    '<td style="text-align:right;padding:2px 4px;font-variant-numeric:tabular-nums;">' . number_format((float) $i->quantity, 2) . '</td>' .
                    '<td style="text-align:right;padding:2px 4px;font-variant-numeric:tabular-nums;">' . number_format((float) $i->unit_rate, 2) . '</td>' .
                    '<td style="text-align:right;padding:2px 4px;font-weight:600;font-variant-numeric:tabular-nums;">' . CurrencyHelper::format($i->amount) . '</td>' .
                    '<td style="text-align:center;padding:2px 4px;color:' . $pctColor . ';font-weight:600;font-size:9px;">' . $itemPct . '%</td>' .
                    '</tr>';
            }

            $tableHtml .= '<tr style="background:#f8fafc;font-weight:700;border-top:1px solid #e2e8f0;">' .
                '<td colspan="5" style="padding:2px 4px;font-size:9px;">Subtotal — ' . $catItems->count() . ' items (' . $pct . '%)</td>' .
                '<td style="text-align:right;padding:2px 4px;font-size:10px;">' . CurrencyHelper::format($catTotal) . '</td><td></td></tr>';
            $tableHtml .= '</tbody></table>';

            $schema[] = Section::make("{$catLabel} ({$catItems->count()}) — " . CurrencyHelper::format($catTotal))
                ->schema([
                    Forms\Components\Placeholder::make('cat_' . ($cat ?: 'uncategorized'))
                        ->content(fn() => new HtmlString($tableHtml))
                        ->columnSpanFull(),
                ])->collapsible()->collapsed();
        }

        // ── Grand total — compact inline ──
        $originalTotal = $items->where('is_variation', false)->sum('amount');
        $variationTotal = $items->where('is_variation', true)->sum('amount');
        $schema[] = Forms\Components\Placeholder::make('grand_summary')
            ->content(fn() => new HtmlString(
                '<div style="display:flex;gap:16px;padding:6px 10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;align-items:center;justify-content:space-between;">' .
                '<div style="display:flex;gap:12px;">' .
                '<div><span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#6b7280;">Original</span> <span style="font-size:13px;font-weight:800;">' . CurrencyHelper::format($originalTotal) . '</span></div>' .
                ($variationTotal > 0 ? '<div><span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#6b7280;">Variations</span> <span style="font-size:13px;font-weight:800;color:#d97706;">' . CurrencyHelper::format($variationTotal) . '</span></div>' : '') .
                '</div>' .
                '<div><span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#059669;">Grand Total</span> <span style="font-size:15px;font-weight:800;color:#059669;">' . CurrencyHelper::format($record->total_value) . '</span> <span style="font-size:9px;color:#6b7280;">(' . $items->count() . ' items)</span></div>' .
                '</div>'
            ))->columnSpanFull();

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

    /* ══════════════════════════════════════════════════════════════════════
       BULK PASTE HELPER — Parses tab or comma-separated data
       ══════════════════════════════════════════════════════════════════════ */
    private function parseBulkData(string $rawData): array
    {
        $lines = array_filter(array_map('trim', preg_split('/\r?\n/', $rawData)));
        $parsed = [];

        foreach ($lines as $line) {
            // Detect delimiter: tab first, then comma
            $delimiter = str_contains($line, "\t") ? "\t" : ",";
            $cols = array_map('trim', explode($delimiter, $line));

            // Skip header rows
            if (
                count($cols) >= 4 && (
                    strtolower($cols[0]) === 'code' || strtolower($cols[0]) === 'item_code' ||
                    strtolower($cols[0]) === '#' || strtolower($cols[0]) === 'item code' ||
                    strtolower($cols[0]) === 'no' || strtolower($cols[0]) === 'no.'
                )
            ) {
                continue;
            }

            // Minimum: code, description, unit, qty, rate
            if (count($cols) < 5)
                continue;

            $qty = floatval(str_replace(',', '', $cols[3]));
            $rate = floatval(str_replace(',', '', $cols[4]));

            $parsed[] = [
                'item_code' => $cols[0],
                'description' => $cols[1],
                'unit' => $cols[2],
                'quantity' => $qty,
                'unit_rate' => $rate,
                'amount' => round($qty * $rate, 2),
                'category' => isset($cols[5]) ? trim($cols[5]) : null,
                'remarks' => isset($cols[6]) ? trim($cols[6]) : null,
            ];
        }

        return $parsed;
    }

    /* ══════════════════════════════════════════════════════════════════════
       BULK UPLOAD HELPER — Parses uploaded CSV file
       ══════════════════════════════════════════════════════════════════════ */
    private function parseCsvFile(string $filePath): array
    {
        $fullPath = storage_path('app/public/' . $filePath);
        if (!file_exists($fullPath)) {
            return [];
        }

        $content = file_get_contents($fullPath);
        // Remove BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        return $this->parseBulkData($content);
    }

    /**
     * Shared logic to insert parsed BOQ items into a BOQ record.
     */
    private function insertParsedItems(array $parsed, Boq $record, bool $isVariation = false): void
    {
        $sortOrder = ($record->items()->max('sort_order') ?? 0);
        $totalAdded = 0;

        foreach ($parsed as $item) {
            $item['sort_order'] = ++$sortOrder;
            $item['is_variation'] = $isVariation;
            $record->items()->create($item);
            $totalAdded += $item['amount'];
        }

        $record->update(['total_value' => $record->items()->sum('amount')]);
        $count = count($parsed);

        Notification::make()
            ->title("✅ {$count} items imported — " . CurrencyHelper::format($totalAdded))
            ->body('Total BOQ value: ' . CurrencyHelper::format($record->fresh()->total_value))
            ->success()->send();
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
                    ->color(fn(string $state) => match ($state) { 'approved' => 'success', 'final' => 'primary', 'priced' => 'info', 'submitted' => 'warning', 'draft' => 'gray', default => 'gray'})->sortable()
                    ->formatStateUsing(fn($state) => Boq::$statuses[$state] ?? $state),
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

                    /* ═══════════════════════════════════════════
                       BULK ADD — Spreadsheet Paste (PRIMARY)
                       ═══════════════════════════════════════════ */
                    \Filament\Actions\Action::make('bulkPaste')
                        ->label('Bulk Add (Paste)')->icon('heroicon-o-clipboard-document-list')->color('primary')
                        ->modalWidth('screen')
                        ->modalHeading(fn(Boq $record) => '📋 Bulk Paste Items — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Placeholder::make('instructions')
                                ->content(fn() => new HtmlString(
                                    '<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:8px 12px;font-size:11px;line-height:1.5;">' .
                                    '<strong style="color:#1d4ed8;">📋 Paste from Excel / Sheets</strong> — ' .
                                    'Columns: <code style="background:#dbeafe;padding:1px 4px;border-radius:3px;font-size:10px;">Code | Description | Unit | Qty | Rate | Category? | Remarks?</code><br>' .
                                    '<span style="color:#6b7280;">Categories: preliminaries, substructure, superstructure, finishes, services, external_works, provisional, dayworks, other</span>' .
                                    '</div>'
                                ))->columnSpanFull(),

                            Forms\Components\Textarea::make('bulk_data')
                                ->label('Paste Data Here')
                                ->placeholder(
                                    "A001\tConcrete Grade 25\tm³\t150\t120.00\tsubstructure\n" .
                                    "A002\tReinforcement Y16\tkg\t5000\t1.85\tsuperstructure\n" .
                                    "A003\tFormwork to columns\tm²\t400\t35.00\tsuperstructure\n" .
                                    "A004\tHardcore filling\tm³\t200\t45.00\tsubstructure\n" .
                                    "A005\tPainting — 2 coats\tm²\t1500\t8.50\tfinishes"
                                )
                                ->rows(10)->required()->columnSpanFull()
                                ->helperText('Select data in Excel → Ctrl+C → Ctrl+V here'),

                            Forms\Components\Toggle::make('is_variation')
                                ->label('Import as variations?')->inline()
                                ->default(false),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $parsed = $this->parseBulkData($data['bulk_data']);

                            if (empty($parsed)) {
                                Notification::make()->title('No valid items found. Check your data format.')->danger()->send();
                                return;
                            }

                            $this->insertParsedItems($parsed, $record, $data['is_variation'] ?? false);
                        }),

                    /* ═══════════════════════════════════════════
                       BULK UPLOAD — CSV / Excel File Upload
                       ═══════════════════════════════════════════ */
                    \Filament\Actions\Action::make('bulkUpload')
                        ->label('Bulk Upload (File)')->icon('heroicon-o-arrow-up-tray')->color('success')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => '📁 Upload BOQ File — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Placeholder::make('upload_instructions')
                                ->content(fn() => new HtmlString(
                                    '<div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:6px;padding:8px 12px;font-size:11px;line-height:1.5;">' .
                                    '<strong style="color:#059669;">📁 Upload a CSV File</strong><br>' .
                                    'Required columns: <code style="background:#d1fae5;padding:1px 4px;border-radius:3px;font-size:10px;">Code, Description, Unit, Qty, Rate</code><br>' .
                                    'Optional columns: <code style="background:#d1fae5;padding:1px 4px;border-radius:3px;font-size:10px;">Category, Remarks</code><br>' .
                                    '<span style="color:#6b7280;">Accepted formats: .csv, .txt — Header rows are auto-skipped</span>' .
                                    '</div>'
                                ))->columnSpanFull(),

                            Forms\Components\FileUpload::make('boq_file')
                                ->label('Select CSV File')
                                ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                                ->directory('boq-uploads/' . now()->format('Y-m'))
                                ->maxSize(5120) // 5MB
                                ->required()
                                ->helperText('Max 5 MB. Supports .csv and .txt files with comma or tab-separated values.'),

                            Forms\Components\Toggle::make('is_variation')
                                ->label('Import as variations?')->inline()
                                ->default(false),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $parsed = $this->parseCsvFile($data['boq_file']);

                            if (empty($parsed)) {
                                Notification::make()
                                    ->title('No valid items found in the uploaded file.')
                                    ->body('Ensure the file has at least 5 columns: Code, Description, Unit, Qty, Rate')
                                    ->danger()->send();
                                return;
                            }

                            $this->insertParsedItems($parsed, $record, $data['is_variation'] ?? false);

                            // Clean up the uploaded file
                            $fullPath = storage_path('app/public/' . $data['boq_file']);
                            if (file_exists($fullPath)) {
                                @unlink($fullPath);
                            }
                        }),

                    /* ═══════════════════════════════════════════
                       QUICK ADD — Single item (fast)
                       ═══════════════════════════════════════════ */
                    \Filament\Actions\Action::make('quickAdd')
                        ->label('Quick Add')->icon('heroicon-o-plus')->color('info')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => 'Quick Add Item — ' . $record->boq_number)
                        ->schema([
                            \Filament\Schemas\Components\Grid::make(4)->schema([
                                Forms\Components\TextInput::make('item_code')->required()->maxLength(20),
                                Forms\Components\TextInput::make('description')->required()->columnSpan(3),
                            ]),
                            \Filament\Schemas\Components\Grid::make(4)->schema([
                                Forms\Components\TextInput::make('unit')->required()->maxLength(10)->default('nr'),
                                Forms\Components\TextInput::make('quantity')->numeric()->required()->default(1),
                                Forms\Components\TextInput::make('unit_rate')->label('Rate')->numeric()->prefix('$')->required()->default(0),
                                Forms\Components\Select::make('category')->options(self::$categories)->searchable()->default('other'),
                            ]),
                            Forms\Components\Toggle::make('is_variation')->label('Variation?')->default(false),
                            Forms\Components\Textarea::make('remarks')->rows(1)->placeholder('Notes...')->columnSpanFull(),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $data['amount'] = round(($data['quantity'] ?? 0) * ($data['unit_rate'] ?? 0), 2);
                            $data['sort_order'] = ($record->items()->max('sort_order') ?? 0) + 1;
                            $record->items()->create($data);
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title("Item {$data['item_code']} added — " . CurrencyHelper::format($data['amount']))->success()->send();
                        }),

                    /* ── Add via Repeater (multi-item form) ── */
                    \Filament\Actions\Action::make('addItems')
                        ->label('Add Multiple')->icon('heroicon-o-plus-circle')->color('info')
                        ->modalWidth('screen')
                        ->modalHeading(fn(Boq $record) => 'Add Items — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Repeater::make('items')
                                ->label('Line Items')
                                ->addActionLabel('+ Add Row')
                                ->defaultItems(1)
                                ->schema([
                                    \Filament\Schemas\Components\Grid::make(7)->schema([
                                        Forms\Components\TextInput::make('item_code')->required()->maxLength(20),
                                        Forms\Components\TextInput::make('description')->required()->columnSpan(2),
                                        Forms\Components\TextInput::make('unit')->required()->maxLength(10),
                                        Forms\Components\TextInput::make('quantity')->numeric()->required()->default(0),
                                        Forms\Components\TextInput::make('unit_rate')->label('Rate ($)')->numeric()->required()->default(0),
                                        Forms\Components\Select::make('category')->options(self::$categories)->searchable(),
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
                            Notification::make()->title("{$count} items added — " . CurrencyHelper::format($totalAdded))->success()->send();
                        }),

                    /* ── Add Variation ── */
                    \Filament\Actions\Action::make('addVariation')
                        ->label('Variation')->icon('heroicon-o-plus')->color('warning')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => 'Add Variation — ' . $record->boq_number)
                        ->schema([
                            \Filament\Schemas\Components\Grid::make(4)->schema([
                                Forms\Components\TextInput::make('item_code')->required()->maxLength(20),
                                Forms\Components\TextInput::make('description')->required()->columnSpan(3),
                            ]),
                            \Filament\Schemas\Components\Grid::make(4)->schema([
                                Forms\Components\TextInput::make('unit')->required()->maxLength(10),
                                Forms\Components\TextInput::make('quantity')->numeric()->required()->default(0),
                                Forms\Components\TextInput::make('unit_rate')->label('Rate ($)')->numeric()->required()->default(0),
                                Forms\Components\Select::make('category')->options(self::$categories)->searchable(),
                            ]),
                            Forms\Components\Textarea::make('remarks')->rows(2)->placeholder('Reason for variation...')->columnSpanFull(),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $data['amount'] = round(($data['quantity'] ?? 0) * ($data['unit_rate'] ?? 0), 2);
                            $data['sort_order'] = ($record->items()->max('sort_order') ?? 0) + 1;
                            $data['is_variation'] = true;
                            $record->items()->create($data);
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title("Variation {$data['item_code']} added — " . CurrencyHelper::format($data['amount']))->success()->send();
                        }),

                    /* ── Track Progress ── */
                    \Filament\Actions\Action::make('trackProgress')
                        ->label('Progress')->icon('heroicon-o-chart-bar')->color('success')
                        ->modalWidth('xl')
                        ->modalHeading(fn(Boq $record) => 'Update Progress — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\Placeholder::make('progress_info')
                                ->content(fn(Boq $record) => new HtmlString(
                                    '<div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:6px;padding:6px 12px;font-size:11px;">' .
                                    '<strong>💡 Bulk Progress:</strong> Update completed quantities for multiple items at once.' .
                                    '</div>'
                                ))->columnSpanFull(),
                            Forms\Components\Repeater::make('progress_items')
                                ->label('Items')
                                ->addable(false)->deletable(false)->reorderable(false)
                                ->schema([
                                    \Filament\Schemas\Components\Grid::make(5)->schema([
                                        Forms\Components\TextInput::make('item_code')->disabled()->columnSpan(1),
                                        Forms\Components\TextInput::make('description')->disabled()->columnSpan(2),
                                        Forms\Components\TextInput::make('total_qty')->label('Total Qty')->disabled()->columnSpan(1),
                                        Forms\Components\TextInput::make('quantity_completed')->label('Completed')->numeric()->required()->columnSpan(1),
                                    ]),
                                    Forms\Components\Hidden::make('item_id'),
                                ])->columnSpanFull(),
                        ])
                        ->fillForm(fn(Boq $record) => [
                            'progress_items' => $record->items()->orderBy('sort_order')->get()->map(fn($i) => [
                                'item_id' => $i->id,
                                'item_code' => $i->item_code,
                                'description' => \Illuminate\Support\Str::limit($i->description, 40),
                                'total_qty' => number_format((float) $i->quantity, 2) . ' ' . $i->unit,
                                'quantity_completed' => $i->quantity_completed,
                            ])->toArray(),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $updated = 0;
                            foreach ($data['progress_items'] ?? [] as $pi) {
                                if (isset($pi['item_id'])) {
                                    BoqItem::where('id', $pi['item_id'])->where('boq_id', $record->id)
                                        ->update(['quantity_completed' => $pi['quantity_completed'] ?? 0]);
                                    $updated++;
                                }
                            }
                            $items = $record->items()->get();
                            $totalQty = $items->sum('quantity');
                            $completedQty = $items->sum('quantity_completed');
                            $pct = $totalQty > 0 ? round(($completedQty / $totalQty) * 100) : 0;
                            Notification::make()->title("{$updated} items updated — {$pct}% overall progress")->success()->send();
                        }),

                    /* ── Revision Snapshot ── */
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

                    /* ── Revision History ── */
                    \Filament\Actions\Action::make('viewRevisions')
                        ->label('History')->icon('heroicon-o-clock')->color('gray')
                        ->modalWidth('screen')
                        ->modalHeading(fn(Boq $record) => 'Revision History — ' . $record->boq_number)
                        ->schema(function (Boq $record) {
                            $revisions = $record->revisions()->with('creator')->orderByDesc('created_at')->get();
                            if ($revisions->isEmpty()) {
                                return [Forms\Components\Placeholder::make('no_revisions')->content('No revisions yet.')];
                            }
                            return $revisions->map(
                                fn(BoqRevision $rev) =>
                                Forms\Components\Placeholder::make('rev_' . $rev->id)
                                    ->label($rev->revision_number . ' — ' . ($rev->creator?->name ?? '?') . ' · ' . $rev->created_at->format('M d, Y H:i'))
                                    ->content(
                                        $rev->change_description . "\n" .
                                        'Value: ' . CurrencyHelper::format($rev->snapshot['total_value'] ?? 0) .
                                        ' | Items: ' . ($rev->snapshot['items_count'] ?? count($rev->snapshot['items'] ?? []))
                                    )
                            )->toArray();
                        })
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    /* ── Approve ── */
                    \Filament\Actions\Action::make('approve')
                        ->label('Approve')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(Boq $record) => !in_array($record->status, ['approved', 'final']))
                        ->requiresConfirmation()
                        ->modalDescription('Mark this BOQ as approved. A revision snapshot will be created automatically.')
                        ->action(function (Boq $record): void {
                            BoqRevision::create([
                                'boq_id' => $record->id,
                                'revision_number' => 'Approval',
                                'change_description' => 'BOQ approved by ' . auth()->user()->name,
                                'snapshot' => [
                                    'total_value' => $record->total_value,
                                    'status' => $record->status,
                                    'items_count' => $record->items()->count(),
                                    'items' => $record->items()->orderBy('sort_order')->get()->toArray(),
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

                    /* ── Status Update ── */
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

                    /* ── Delete Items ── */
                    \Filament\Actions\Action::make('deleteItems')
                        ->label('Delete Items')->icon('heroicon-o-trash')->color('danger')
                        ->modalHeading(fn(Boq $record) => 'Delete Items — ' . $record->boq_number)
                        ->schema([
                            Forms\Components\CheckboxList::make('item_ids')
                                ->label('Select items to remove')
                                ->options(fn(Boq $record) => $record->items->mapWithKeys(fn($i) =>
                                    [$i->id => ($i->is_variation ? '🔸 ' : '') . $i->item_code . ' — ' . \Illuminate\Support\Str::limit($i->description, 40) . ' (' . CurrencyHelper::format($i->amount) . ')']))
                                ->required()->searchable()->columns(1),
                        ])
                        ->action(function (array $data, Boq $record): void {
                            $count = count($data['item_ids']);
                            BoqItem::whereIn('id', $data['item_ids'])->where('boq_id', $record->id)->delete();
                            $record->update(['total_value' => $record->items()->sum('amount')]);
                            Notification::make()->title("{$count} items deleted")->danger()->send();
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

                    /* ── Delete BOQ ── */
                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(Boq $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('boq', fn() => Boq::query()->where('cde_project_id', $this->pid())->with(['contract', 'creator']), [
                    'boq_number' => 'BOQ #',
                    'name' => 'Name',
                    'contract.title' => 'Contract',
                    'status' => 'Status',
                    'total_value' => 'Total Value',
                    'currency' => 'Currency',
                    'description' => 'Description',
                    'notes' => 'Notes',
                    'creator.name' => 'Created By',
                    'created_at' => 'Created At',
                ]),
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
            ->emptyStateDescription('Create BOQs and bulk-paste line items from your spreadsheets.')
            ->emptyStateIcon('heroicon-o-calculator')
            ->striped()->paginated([10, 25, 50]);
    }
}
