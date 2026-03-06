<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Boq;
use App\Models\Certificate;
use App\Models\Contract;
use App\Models\ContractPayment;
use App\Models\User;
use App\Models\Vendor;
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

class CostContractsPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, ExportsTableCsv;

    protected static string $moduleCode = 'cost_contracts';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $title = 'Cost & Contract Management';
    protected string $view = 'filament.app.pages.modules.cost-contracts';

    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    // ─────────────────────────────────────────────
    // Stats — 5 cards including retainage
    // ─────────────────────────────────────────────
    public function getStats(): array
    {
        $pid = $this->pid();
        $base = Contract::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $active = (clone $base)->where('status', 'active')->count();
        $origVal = (clone $base)->sum('original_value');
        $revisedVal = (clone $base)->sum('revised_value');
        $paid = (clone $base)->sum('amount_paid');
        $retainageHeld = (clone $base)->sum('retainage_held');

        return [
            [
                'label' => 'Total Contracts',
                'value' => $total,
                'sub' => $active . ' active',
                'sub_type' => 'info',
                'primary' => true,
                'icon' => 'heroicon-o-document-text',
            ],
            [
                'label' => 'Original Value',
                'value' => CurrencyHelper::formatCompact($origVal),
                'full_value' => CurrencyHelper::format($origVal, 0),
                'sub' => 'Contract sum',
                'sub_type' => 'neutral',
                'icon' => 'heroicon-o-currency-dollar',
                'icon_color' => '#6366f1',
                'icon_bg' => '#eef2ff',
            ],
            [
                'label' => 'Revised Value',
                'value' => CurrencyHelper::formatCompact($revisedVal),
                'full_value' => CurrencyHelper::format($revisedVal, 0),
                'sub' => $revisedVal > $origVal ? 'Exceeded original' : 'Within budget',
                'sub_type' => $revisedVal > $origVal ? 'danger' : 'success',
                'icon' => 'heroicon-o-arrow-trending-up',
                'icon_color' => '#d97706',
                'icon_bg' => '#fffbeb',
            ],
            [
                'label' => 'Amount Paid',
                'value' => CurrencyHelper::formatCompact($paid),
                'full_value' => CurrencyHelper::format($paid, 0),
                'sub' => $revisedVal > 0 ? round(($paid / $revisedVal) * 100) . '% of revised' : 'No contracts',
                'sub_type' => 'info',
                'icon' => 'heroicon-o-banknotes',
                'icon_color' => '#059669',
                'icon_bg' => '#ecfdf5',
            ],
            [
                'label' => 'Retainage Held',
                'value' => CurrencyHelper::formatCompact($retainageHeld),
                'full_value' => CurrencyHelper::format($retainageHeld, 0),
                'sub' => 'Across all contracts',
                'sub_type' => 'neutral',
                'icon' => 'heroicon-o-lock-closed',
                'icon_color' => '#9333ea',
                'icon_bg' => '#faf5ff',
            ],
        ];
    }

    public function getContractSummary(): array
    {
        $pid = $this->pid();
        $contracts = Contract::where('cde_project_id', $pid)->get();
        $totalOriginal = $contracts->sum('original_value');
        $totalRevised = $contracts->sum('revised_value');
        $totalPaid = $contracts->sum('amount_paid');
        $totalRetainage = $contracts->sum('retainage_held');
        $balance = max(0, $totalRevised - $totalPaid);
        $paidPercent = $totalRevised > 0 ? round(($totalPaid / $totalRevised) * 100) : 0;

        return [
            'original' => $totalOriginal,
            'revised' => $totalRevised,
            'paid' => $totalPaid,
            'retainage' => $totalRetainage,
            'balance' => $balance,
            'paid_percent' => $paidPercent,
            'active' => $contracts->where('status', 'active')->count(),
            'completed' => $contracts->where('status', 'completed')->count(),
            'total' => $contracts->count(),
        ];
    }

    // ─────────────────────────────────────────────
    // Contract Form Schema
    // ─────────────────────────────────────────────
    private function contractFormSchema(bool $isCreate = false): array
    {
        $cid = $this->cid();
        return [
            Section::make('Contract Details')->schema([
                Forms\Components\TextInput::make('contract_number')->label('Contract #')
                    ->default(fn() => $isCreate ? 'CON-' . str_pad((string) (Contract::where('cde_project_id', $this->pid())->count() + 1), 4, '0', STR_PAD_LEFT) : null)
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('vendor_id')->label('Vendor / Contractor')
                    ->options(fn() => Vendor::where('company_id', $cid)->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()->preload()->nullable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->email(),
                        Forms\Components\TextInput::make('phone')->tel(),
                        Forms\Components\TextInput::make('contact_person')->maxLength(255),
                    ])
                    ->createOptionUsing(fn(array $data) => Vendor::create(array_merge($data, ['company_id' => $cid, 'is_active' => true]))->id),
                Forms\Components\Select::make('type')->options([
                    'main' => 'Main Contract',
                    'sub' => 'Sub-Contract',
                    'supply' => 'Supply Only',
                    'labour' => 'Labour Only',
                    'professional' => 'Professional Services',
                    'other' => 'Other',
                ])->required()->default('main'),
                Forms\Components\Select::make('status')->options(Contract::$statuses)->required()->default($isCreate ? 'draft' : null),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
            ])->columns(2),
            Section::make('Financial Details')->schema([
                Forms\Components\TextInput::make('original_value')->label('Original Value')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required()->default(0),
                Forms\Components\TextInput::make('revised_value')->label('Revised Value')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0),
                Forms\Components\TextInput::make('amount_paid')->label('Amount Paid')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0)->visible(!$isCreate),
                Forms\Components\TextInput::make('retainage_percent')->label('Retainage (%)')->numeric()->suffix('%')->default(0)
                    ->helperText('Percentage withheld from each payment'),
                Forms\Components\TextInput::make('retainage_held')->label('Retainage Held')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0)->visible(!$isCreate),
                Forms\Components\TextInput::make('retainage_released')->label('Retainage Released')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0)->visible(!$isCreate),
            ])->columns(2),
            Section::make('Scope & Description')->schema([
                Forms\Components\RichEditor::make('description')->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])->columnSpanFull(),
                Forms\Components\Textarea::make('scope_of_work')->label('Scope of Work')->rows(3)->columnSpanFull(),
            ])->collapsed(!$isCreate),
        ];
    }

    // ─────────────────────────────────────────────
    // Rich View Detail Modal
    // ─────────────────────────────────────────────
    private function viewDetailSchema(Contract $record): array
    {
        $record->load(['vendor', 'creator', 'payments.creator', 'boqs.items']);

        // ── Info bar ──
        $infoFields = [
            ['Contract', $record->contract_number],
            ['Status', Contract::$statuses[$record->status] ?? $record->status],
            ['Type', ucfirst($record->type ?? '—')],
            ['Vendor', $record->vendor?->name ?? '—'],
            ['Start', $record->start_date?->format('M d, Y') ?? '—'],
            ['End', $record->end_date?->format('M d, Y') ?? '—'],
            ['By', $record->creator?->name ?? '—'],
        ];
        $infoCells = collect($infoFields)->map(
            fn($c) =>
            '<div style="display:flex;gap:3px;align-items:baseline;">' .
            '<span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:#9ca3af;">' . $c[0] . '</span>' .
            '<span style="font-size:11px;font-weight:600;color:#1f2937;">' . e($c[1]) . '</span></div>'
        )->join('');

        // ── Financial summary cards ──
        $balance = $record->balance;
        $paidPct = $record->payment_progress;
        $durationPct = $record->duration_progress;
        $retHeld = (float) ($record->retainage_held ?? 0);
        $retReleased = (float) ($record->retainage_released ?? 0);
        $retRemaining = $retHeld - $retReleased;

        $finCards = [
            ['Original Value', CurrencyHelper::format($record->original_value, 0), '#6366f1', '#eef2ff'],
            ['Revised Value', CurrencyHelper::format($record->revised_value, 0), '#d97706', '#fffbeb'],
            ['Amount Paid', CurrencyHelper::format($record->amount_paid, 0), '#059669', '#ecfdf5'],
            ['Balance', CurrencyHelper::format($balance, 0), $balance < 0 ? '#dc2626' : '#0ea5e9', $balance < 0 ? '#fef2f2' : '#f0f9ff'],
        ];
        $finHtml = '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem;margin-bottom:0.5rem;">';
        foreach ($finCards as $fc) {
            $finHtml .= '<div style="padding:0.5rem 0.75rem;border-radius:0.5rem;border:1px solid ' . $fc[2] . '22;background:' . $fc[3] . ';">' .
                '<div style="font-size:0.55rem;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:#6b7280;margin-bottom:2px;">' . $fc[0] . '</div>' .
                '<div style="font-size:0.95rem;font-weight:800;color:' . $fc[2] . ';">' . $fc[1] . '</div>' .
                '</div>';
        }
        $finHtml .= '</div>';

        // ── Payment progress bar ──
        $barColor = $paidPct >= 100 ? '#059669' : ($paidPct >= 75 ? '#3b82f6' : ($paidPct >= 50 ? '#d97706' : '#6366f1'));
        $finHtml .= '<div style="margin-bottom:0.5rem;">' .
            '<div style="display:flex;justify-content:space-between;font-size:10px;font-weight:600;color:#6b7280;margin-bottom:3px;">' .
            '<span>Payment Progress</span><span style="color:' . $barColor . ';">' . $paidPct . '%</span></div>' .
            '<div style="height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">' .
            '<div style="height:100%;width:' . min($paidPct, 100) . '%;background:' . $barColor . ';border-radius:3px;transition:width 0.3s;"></div>' .
            '</div></div>';

        // ── Duration progress bar ──
        if ($durationPct !== null) {
            $durColor = $durationPct >= 100 ? '#dc2626' : ($durationPct >= 75 ? '#d97706' : '#3b82f6');
            $daysLeft = $record->end_date?->diffInDays(now(), false);
            $durLabel = $daysLeft !== null && $daysLeft < 0 ? abs($daysLeft) . ' days left' : ($daysLeft > 0 ? $daysLeft . ' days overdue' : 'Ending today');
            $finHtml .= '<div style="margin-bottom:0.5rem;">' .
                '<div style="display:flex;justify-content:space-between;font-size:10px;font-weight:600;color:#6b7280;margin-bottom:3px;">' .
                '<span>Contract Duration</span><span style="color:' . $durColor . ';">' . $durationPct . '% — ' . $durLabel . '</span></div>' .
                '<div style="height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">' .
                '<div style="height:100%;width:' . min($durationPct, 100) . '%;background:' . $durColor . ';border-radius:3px;"></div>' .
                '</div></div>';
        }

        // ── Retainage summary ──
        if ($retHeld > 0 || $record->retainage_percent > 0) {
            $finHtml .= '<div style="display:flex;gap:16px;padding:6px 10px;background:#faf5ff;border:1px solid #e9d5ff;border-radius:6px;font-size:11px;margin-bottom:0.5rem;">' .
                '<div><span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#9ca3af;">Rate</span> <span style="font-weight:700;">' . ($record->retainage_percent ?? 0) . '%</span></div>' .
                '<div><span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#9ca3af;">Held</span> <span style="font-weight:700;">' . CurrencyHelper::format($retHeld) . '</span></div>' .
                '<div><span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#9ca3af;">Released</span> <span style="font-weight:700;">' . CurrencyHelper::format($retReleased) . '</span></div>' .
                '<div><span style="font-size:8px;font-weight:700;text-transform:uppercase;color:#9ca3af;">Remaining</span> <span style="font-weight:700;color:#9333ea;">' . CurrencyHelper::format($retRemaining) . '</span></div>' .
                '</div>';
        }

        // ── Linked BOQs ──
        $boqHtml = '';
        if ($record->boqs->isNotEmpty()) {
            $boqHtml = '<div style="margin-bottom:0.5rem;">' .
                '<div style="font-size:0.65rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:4px;">Linked BOQs</div>' .
                '<div style="display:flex;flex-wrap:wrap;gap:0.4rem;">';
            foreach ($record->boqs as $boq) {
                $boqTotal = CurrencyHelper::format($boq->total_value, 0);
                $boqItems = $boq->items->count();
                $boqHtml .= '<div style="padding:4px 8px;border-radius:6px;border:1px solid #e5e7eb;background:#f9fafb;font-size:11px;">' .
                    '<span style="font-weight:700;">' . e($boq->boq_number) . '</span> ' .
                    '<span style="color:#6b7280;">' . e($boq->name) . '</span> · ' .
                    '<span style="font-weight:600;color:#059669;">' . $boqTotal . '</span> · ' .
                    '<span style="color:#9ca3af;">' . $boqItems . ' items</span>' .
                    '</div>';
            }
            $boqHtml .= '</div></div>';
        }

        // ── Payment History ──
        $payHtml = '';
        $payments = $record->payments;
        if ($payments->isNotEmpty()) {
            $payHtml = '<div>' .
                '<div style="font-size:0.65rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:4px;">Payment History (' . $payments->count() . ')</div>' .
                '<div style="max-height:180px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:6px;">' .
                '<table style="width:100%;border-collapse:collapse;font-size:11px;">' .
                '<thead><tr style="background:#f1f5f9;border-bottom:1px solid #cbd5e1;position:sticky;top:0;">' .
                '<th style="text-align:left;padding:4px 8px;font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b;">Date</th>' .
                '<th style="text-align:left;padding:4px 8px;font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b;">Ref</th>' .
                '<th style="text-align:left;padding:4px 8px;font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b;">Type</th>' .
                '<th style="text-align:right;padding:4px 8px;font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b;">Amount</th>' .
                '<th style="text-align:left;padding:4px 8px;font-size:9px;font-weight:700;text-transform:uppercase;color:#64748b;">By</th>' .
                '</tr></thead><tbody>';

            foreach ($payments as $pay) {
                $typeColor = match ($pay->type) {
                    'deduction' => '#dc2626',
                    'retention_release' => '#9333ea',
                    'advance' => '#d97706',
                    default => '#059669',
                };
                $payHtml .= '<tr style="border-bottom:1px solid #f1f5f9;">' .
                    '<td style="padding:3px 8px;font-variant-numeric:tabular-nums;">' . $pay->payment_date->format('M d, Y') . '</td>' .
                    '<td style="padding:3px 8px;font-family:monospace;font-size:10px;">' . e($pay->reference ?? '—') . '</td>' .
                    '<td style="padding:3px 8px;"><span style="font-size:9px;font-weight:600;color:' . $typeColor . ';text-transform:uppercase;">' . (ContractPayment::$types[$pay->type] ?? $pay->type) . '</span></td>' .
                    '<td style="padding:3px 8px;text-align:right;font-weight:600;font-variant-numeric:tabular-nums;">' . CurrencyHelper::format($pay->amount) . '</td>' .
                    '<td style="padding:3px 8px;font-size:10px;color:#6b7280;">' . ($pay->creator?->name ?? '—') . '</td>' .
                    '</tr>';
            }

            $payHtml .= '</tbody></table></div></div>';
        } else {
            $payHtml = '<div style="padding:12px;text-align:center;color:#9ca3af;font-size:11px;border:1px dashed #e2e8f0;border-radius:6px;">' .
                'No payment records yet. Click ⋯ → <strong>Payment</strong> to record one.</div>';
        }

        // ── Scope of Work ──
        $scopeHtml = '';
        if (!empty($record->scope_of_work)) {
            $scopeHtml = '<div style="margin-top:0.5rem;">' .
                '<div style="font-size:0.65rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#6b7280;margin-bottom:4px;">Scope of Work</div>' .
                '<div style="padding:8px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;color:#334155;white-space:pre-wrap;">' . e($record->scope_of_work) . '</div>' .
                '</div>';
        }

        return [
            Forms\Components\Placeholder::make('info_bar')
                ->content(fn() => new HtmlString(
                    '<div style="display:flex;flex-wrap:wrap;gap:8px 16px;padding:6px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;">' .
                    $infoCells . '</div>'
                ))->columnSpanFull(),

            Forms\Components\Placeholder::make('financials')
                ->content(fn() => new HtmlString($finHtml))
                ->columnSpanFull(),

            Forms\Components\Placeholder::make('linked_boqs')
                ->content(fn() => new HtmlString($boqHtml))
                ->columnSpanFull()
                ->visible($boqHtml !== ''),

            Forms\Components\Placeholder::make('payment_history')
                ->content(fn() => new HtmlString($payHtml))
                ->columnSpanFull(),

            Forms\Components\Placeholder::make('scope')
                ->content(fn() => new HtmlString($scopeHtml))
                ->columnSpanFull()
                ->visible($scopeHtml !== ''),
        ];
    }

    // ─────────────────────────────────────────────
    // Header Actions
    // ─────────────────────────────────────────────
    protected function getHeaderActions(): array
    {
        return [
            Action::make('createContract')
                ->label('New Contract')->icon('heroicon-o-plus-circle')->color('primary')
                ->modalWidth('4xl')
                ->schema($this->contractFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['created_by'] = auth()->id();
                    if (empty($data['revised_value']))
                        $data['revised_value'] = $data['original_value'];
                    Contract::create($data);
                    Notification::make()->title('Contract created')->success()->send();
                }),
            Action::make('addCertificate')
                ->label('Add Certificate')->icon('heroicon-o-shield-check')->color('success')
                ->modalWidth('2xl')
                ->schema([
                    Section::make('Certificate Details')->schema([
                        Forms\Components\Select::make('type')->options(Certificate::$types)->required(),
                        Forms\Components\TextInput::make('name')->required()->maxLength(255)
                            ->placeholder('e.g. Public Liability Insurance'),
                        Forms\Components\TextInput::make('reference_number')->maxLength(100),
                        Forms\Components\TextInput::make('issuing_authority')->maxLength(255),
                        Forms\Components\DatePicker::make('issue_date'),
                        Forms\Components\DatePicker::make('expiry_date'),
                        Forms\Components\Select::make('contract_id')->label('Linked Contract')
                            ->options(fn() => Contract::where('cde_project_id', $this->pid())->pluck('title', 'id'))
                            ->searchable()->nullable(),
                        Forms\Components\Select::make('vendor_id')->label('Vendor')
                            ->options(fn() => Vendor::where('company_id', $this->cid())->pluck('name', 'id'))
                            ->searchable()->nullable(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Certificate File')
                            ->directory('certificates')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(5120),
                        Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                    ])->columns(2),
                ])
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['created_by'] = auth()->id();
                    $data['status'] = 'active';
                    Certificate::create($data);
                    Notification::make()->title('Certificate added')->success()->send();
                }),
            Action::make('viewCertificates')
                ->label('Certificates')->icon('heroicon-o-clipboard-document-list')->color('gray')
                ->modalWidth('4xl')
                ->modalHeading('Project Certificates')
                ->schema(fn() => $this->getCertificateSchema())
                ->modalSubmitAction(false)->modalCancelActionLabel('Close'),
        ];
    }

    // ─────────────────────────────────────────────
    // Table — with Balance column, duration bar, row-click
    // ─────────────────────────────────────────────
    public function table(Table $table): Table
    {
        return $table
            ->query(Contract::query()->where('cde_project_id', $this->pid())->with(['vendor', 'creator', 'boqs']))
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')->label('Contract #')->searchable()->sortable()->weight('bold')->toggleable()
                    ->icon('heroicon-o-document-text')->copyable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35)->tooltip(fn(Contract $record) => $record->title)->toggleable(),
                Tables\Columns\TextColumn::make('vendor.name')->label('Vendor')->placeholder('—')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('type')->badge()->color('info')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'active' => 'success', 'completed' => 'primary', 'draft' => 'gray', 'terminated' => 'danger', 'suspended' => 'warning', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('original_value')->label('Original')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('revised_value')->label('Revised')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable()
                    ->color(fn(Contract $record) => ($record->revised_value ?? 0) > ($record->original_value ?? 0) ? 'danger' : null),
                Tables\Columns\TextColumn::make('amount_paid')->label('Paid')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable(),

                // ── Balance Remaining (NEW) ──
                Tables\Columns\TextColumn::make('balance_remaining')->label('Balance')->toggleable()
                    ->state(fn(Contract $record) => CurrencyHelper::format($record->balance, 0))
                    ->color(fn(Contract $record) => $record->balance < 0 ? 'danger' : ($record->balance == 0 ? 'success' : null)),

                // ── Payment % (visual bar) ──
                Tables\Columns\TextColumn::make('payment_progress')->label('% Paid')->toggleable()
                    ->state(fn(Contract $record) => $record->payment_progress . '%')
                    ->color(fn(Contract $record) => $record->payment_progress >= 100 ? 'success' : ($record->payment_progress >= 50 ? 'info' : null)),

                // ── Contract Duration bar (NEW) ──
                Tables\Columns\TextColumn::make('duration')->label('Duration')->toggleable()
                    ->state(function (Contract $record) {
                        if (!$record->start_date || !$record->end_date)
                            return '—';
                        $pct = $record->duration_progress;
                        $daysLeft = $record->end_date->diffInDays(now(), false);
                        $label = $daysLeft < 0 ? abs($daysLeft) . 'd left' : ($daysLeft > 0 ? $daysLeft . 'd over' : 'Today');
                        return $pct . '% · ' . $label;
                    })
                    ->color(function (Contract $record) {
                        $pct = $record->duration_progress;
                        return $pct === null ? null : ($pct >= 100 ? 'danger' : ($pct >= 75 ? 'warning' : null));
                    }),

                Tables\Columns\TextColumn::make('retainage_held')->label('Ret. Held')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Contract::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('type')->options([
                    'main' => 'Main',
                    'sub' => 'Sub-Contract',
                    'supply' => 'Supply',
                    'labour' => 'Labour',
                    'professional' => 'Professional',
                ]),
                Tables\Filters\SelectFilter::make('vendor_id')->label('Vendor')
                    ->options(fn() => Vendor::where('company_id', $this->cid())->pluck('name', 'id')),
            ])
            ->recordAction('viewDetail')
            ->recordActions([
                /* ── View Detail (row-click target) ── */
                \Filament\Actions\Action::make('viewDetail')
                    ->label('View')->icon('heroicon-o-eye')->color('gray')->modalWidth('screen')
                    ->modalHeading(fn(Contract $record) => $record->contract_number . ' — ' . $record->title)
                    ->schema(fn(Contract $record) => $this->viewDetailSchema($record))
                    ->fillForm(fn(Contract $record) => $record->toArray())
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\ActionGroup::make([

                    /* ── Record Payment (now with proper tracking) ── */
                    \Filament\Actions\Action::make('recordPayment')
                        ->label('Payment')->icon('heroicon-o-banknotes')->color('success')
                        ->schema([
                            Forms\Components\TextInput::make('amount')->label('Payment Amount')
                                ->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required(),
                            Forms\Components\Select::make('type')->label('Payment Type')
                                ->options(ContractPayment::$types)->required()->default('payment'),
                            Forms\Components\DatePicker::make('payment_date')->required()->default(now()),
                            Forms\Components\TextInput::make('reference')->label('Reference / Cheque #')->maxLength(100),
                            Forms\Components\Select::make('payment_method')->label('Method')
                                ->options(ContractPayment::$methods)->nullable(),
                            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                        ])
                        ->action(function (array $data, Contract $record): void {
                            $amt = (float) $data['amount'];
                            // Record payment entry
                            ContractPayment::create([
                                'contract_id' => $record->id,
                                'company_id' => $record->company_id,
                                'amount' => $amt,
                                'type' => $data['type'],
                                'payment_date' => $data['payment_date'],
                                'reference' => $data['reference'] ?? null,
                                'payment_method' => $data['payment_method'] ?? null,
                                'notes' => $data['notes'] ?? null,
                                'created_by' => auth()->id(),
                            ]);
                            // Update contract totals
                            if ($data['type'] === 'deduction') {
                                $record->update(['amount_paid' => max(0, ($record->amount_paid ?? 0) - $amt)]);
                            } elseif ($data['type'] === 'retention_release') {
                                $record->update(['retainage_released' => ($record->retainage_released ?? 0) + $amt]);
                            } else {
                                $newPaid = ($record->amount_paid ?? 0) + $amt;
                                $updateData = ['amount_paid' => $newPaid];
                                // Auto-calculate retainage if rate is set
                                if ($record->retainage_percent > 0) {
                                    $retAmount = $amt * ($record->retainage_percent / 100);
                                    $updateData['retainage_held'] = ($record->retainage_held ?? 0) + $retAmount;
                                }
                                $record->update($updateData);
                            }
                            Notification::make()->title(CurrencyHelper::format($amt) . ' recorded as ' . (ContractPayment::$types[$data['type']] ?? $data['type']))->success()->send();
                        }),

                    \Filament\Actions\Action::make('addVariation')
                        ->label('Variation')->icon('heroicon-o-plus-circle')->color('warning')
                        ->modalHeading(fn(Contract $record) => 'Add Variation — ' . $record->contract_number)
                        ->schema([
                            Forms\Components\TextInput::make('variation_amount')->label('Variation Amount')
                                ->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required()
                                ->helperText('Positive = addition, negative = deduction'),
                            Forms\Components\Textarea::make('reason')->label('Reason for Variation')->rows(2)->required(),
                        ])
                        ->action(function (array $data, Contract $record): void {
                            $newRevised = ($record->revised_value ?? $record->original_value ?? 0) + $data['variation_amount'];
                            $notes = $record->description ? $record->description . "\n" : '';
                            $notes .= '[Variation ' . now()->format('M d') . ' — ' . ($data['variation_amount'] >= 0 ? '+' : '') . CurrencyHelper::format($data['variation_amount']) . '] ' . $data['reason'];
                            $record->update(['revised_value' => $newRevised, 'description' => $notes]);
                            Notification::make()->title('Variation applied. New revised value: ' . CurrencyHelper::format($newRevised))->success()->send();
                        }),

                    \Filament\Actions\Action::make('updateStatus')
                        ->label('Status')->icon('heroicon-o-arrow-path')->color('warning')
                        ->schema([Forms\Components\Select::make('status')->options(Contract::$statuses)->required()])
                        ->fillForm(fn(Contract $record) => ['status' => $record->status])
                        ->action(function (array $data, Contract $record): void {
                            $record->update($data);
                            Notification::make()->title('Status → ' . Contract::$statuses[$data['status']])->success()->send();
                        }),

                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('4xl')
                        ->schema($this->contractFormSchema())
                        ->fillForm(fn(Contract $record) => $record->toArray())
                        ->action(function (array $data, Contract $record): void {
                            $record->update($data);
                            Notification::make()->title('Contract updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Contract $record): void {
                            $new = $record->replicate();
                            $new->contract_number = 'CON-' . str_pad((string) (Contract::where('cde_project_id', $record->cde_project_id)->count() + 1), 4, '0', STR_PAD_LEFT);
                            $new->status = 'draft';
                            $new->amount_paid = 0;
                            $new->created_by = auth()->id();
                            $new->save();
                            Notification::make()->title('Contract duplicated as ' . $new->contract_number)->success()->send();
                        }),

                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(Contract $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('contracts', fn() => Contract::query()->where('cde_project_id', $this->pid())->with(['vendor', 'creator']), [
                    'contract_number' => 'Contract #',
                    'title' => 'Title',
                    'vendor.name' => 'Vendor',
                    'type' => 'Type',
                    'status' => 'Status',
                    'original_value' => 'Original Value',
                    'revised_value' => 'Revised Value',
                    'amount_paid' => 'Amount Paid',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'created_at' => 'Created At',
                ]),
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkStatus')->label('Update Status')->icon('heroicon-o-arrow-path')
                        ->schema([Forms\Components\Select::make('status')->options(Contract::$statuses)->required()])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r)
                                $r->update($data);
                            Notification::make()->title($records->count() . ' contracts updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Contracts')
            ->emptyStateDescription('Create contracts to track costs and payments.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->striped()->paginated([10, 25, 50]);
    }

    // ─────────────────────────────────────────────
    // Certificate Schema
    // ─────────────────────────────────────────────
    private function getCertificateSchema(): array
    {
        $certs = Certificate::where('cde_project_id', $this->pid())
            ->with(['contract', 'vendor'])
            ->orderBy('expiry_date')
            ->get();

        if ($certs->isEmpty()) {
            return [
                Forms\Components\Placeholder::make('no_certs')
                    ->content('No certificates added yet. Click "Add Certificate" to start tracking.')
                    ->columnSpanFull(),
            ];
        }

        $rows = $certs->map(function ($cert) {
            $days = $cert->daysUntilExpiry();
            $expiryLabel = $cert->expiry_date ? $cert->expiry_date->format('M d, Y') : 'No expiry';
            $statusColor = $cert->isExpired() ? '#dc2626'
                : ($cert->isExpiringSoon() ? '#d97706' : '#059669');
            $statusLabel = $cert->isExpired() ? 'EXPIRED'
                : ($cert->isExpiringSoon() ? "Exp. in {$days}d" : 'Active');

            return '<tr style="border-bottom:1px solid #f1f5f9;">' .
                '<td style="padding:4px 8px;font-weight:600;font-size:11px;">' . e($cert->name) . '</td>' .
                '<td style="padding:4px 8px;font-size:11px;">' . (Certificate::$types[$cert->type] ?? $cert->type) . '</td>' .
                '<td style="padding:4px 8px;font-size:11px;">' . e($cert->reference_number ?? '—') . '</td>' .
                '<td style="padding:4px 8px;font-size:11px;">' . ($cert->vendor?->name ?? $cert->contract?->title ?? '—') . '</td>' .
                '<td style="padding:4px 8px;font-size:11px;">' . $expiryLabel . '</td>' .
                '<td style="padding:4px 8px;font-size:11px;font-weight:600;color:' . $statusColor . ';">' . $statusLabel . '</td>' .
                '</tr>';
        })->join('');

        $html = '<table style="width:100%;border-collapse:collapse;font-size:12px;">' .
            '<thead><tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">' .
            '<th style="text-align:left;padding:4px 8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Name</th>' .
            '<th style="text-align:left;padding:4px 8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Type</th>' .
            '<th style="text-align:left;padding:4px 8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Ref #</th>' .
            '<th style="text-align:left;padding:4px 8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Linked To</th>' .
            '<th style="text-align:left;padding:4px 8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Expires</th>' .
            '<th style="text-align:left;padding:4px 8px;font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;">Status</th>' .
            '</tr></thead><tbody>' . $rows . '</tbody></table>';

        $expired = $certs->filter(fn($c) => $c->isExpired())->count();
        $expiringSoon = $certs->filter(fn($c) => $c->isExpiringSoon())->count();

        $summary = '<div style="display:flex;gap:16px;padding:8px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;margin-bottom:8px;font-size:12px;">' .
            '<span><strong>' . $certs->count() . '</strong> total</span>' .
            ($expired > 0 ? '<span style="color:#dc2626;font-weight:600;">⚠ ' . $expired . ' expired</span>' : '') .
            ($expiringSoon > 0 ? '<span style="color:#d97706;font-weight:600;">⏰ ' . $expiringSoon . ' expiring soon</span>' : '') .
            ($expired === 0 && $expiringSoon === 0 ? '<span style="color:#059669;">✓ All current</span>' : '') .
            '</div>';

        return [
            Forms\Components\Placeholder::make('cert_table')
                ->content(fn() => new HtmlString($summary . $html))
                ->columnSpanFull(),
        ];
    }
}
