<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\User;
use App\Models\WorkOrder;
use App\Support\CurrencyHelper;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

class FinancialsPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, ExportsTableCsv;

    protected static string $moduleCode = 'financials';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Financials';
    protected static ?string $title = 'Financial Tracking';
    protected string $view = 'filament.app.pages.modules.financials';

    public string $activeTab = 'invoices';

    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    // ─────────────────────────────────────────────
    // Stats — standard Heroicons
    // ─────────────────────────────────────────────
    public function getStats(): array
    {
        $pid = $this->pid();

        $stats = \DB::selectOne("
            SELECT
                COALESCE((SELECT SUM(total_amount) FROM invoices WHERE cde_project_id = ?), 0) as invoiced,
                COALESCE((SELECT SUM(amount) FROM invoice_payments WHERE cde_project_id = ?), 0) as received,
                COALESCE((SELECT SUM(total_amount - amount_paid) FROM invoices WHERE cde_project_id = ? AND status NOT IN ('paid','cancelled')), 0) as outstanding,
                COALESCE((SELECT SUM(amount) FROM expenses WHERE cde_project_id = ? AND status != 'rejected'), 0) as expenses,
                (SELECT COUNT(*) FROM invoices WHERE cde_project_id = ? AND status NOT IN ('paid','cancelled') AND due_date IS NOT NULL AND due_date < NOW()) as overdue
        ", [$pid, $pid, $pid, $pid, $pid]);

        $invoiced = (float) $stats->invoiced;
        $received = (float) $stats->received;
        $outstanding = (float) $stats->outstanding;
        $expenses = (float) $stats->expenses;
        $overdue = (int) $stats->overdue;

        $balance = $received - $expenses;
        $collectionRate = $invoiced > 0 ? round(($received / $invoiced) * 100, 1) : 0;
        $budgetUtil = $invoiced > 0 ? round(($expenses / $invoiced) * 100, 1) : 0;

        return [
            [
                'label' => 'Total Invoiced',
                'value' => CurrencyHelper::formatCompact($invoiced),
                'full_value' => CurrencyHelper::format($invoiced, 0),
                'sub' => $overdue > 0 ? $overdue . ' overdue' : 'All on track',
                'sub_type' => $overdue > 0 ? 'danger' : 'success',
                'icon' => 'heroicon-o-document-text',
                'icon_color' => '#6366f1',
                'icon_bg' => '#eef2ff',
            ],
            [
                'label' => 'Amount Received',
                'value' => CurrencyHelper::formatCompact($received),
                'full_value' => CurrencyHelper::format($received, 0),
                'sub' => 'Collection rate: ' . $collectionRate . '%',
                'sub_type' => $collectionRate >= 80 ? 'success' : ($collectionRate >= 50 ? 'warning' : 'danger'),
                'icon' => 'heroicon-o-banknotes',
                'icon_color' => '#10b981',
                'icon_bg' => '#ecfdf5',
            ],
            [
                'label' => 'Total Expenses',
                'value' => CurrencyHelper::formatCompact($expenses),
                'full_value' => CurrencyHelper::format($expenses, 0),
                'sub' => 'Utilisation: ' . $budgetUtil . '% of invoiced',
                'sub_type' => $budgetUtil <= 70 ? 'success' : ($budgetUtil <= 90 ? 'warning' : 'danger'),
                'icon' => 'heroicon-o-arrow-trending-up',
                'icon_color' => '#ef4444',
                'icon_bg' => '#fef2f2',
            ],
            [
                'label' => 'Net Balance',
                'value' => CurrencyHelper::formatCompact($balance),
                'full_value' => CurrencyHelper::format($balance, 0),
                'sub' => 'Revenue - Expenses',
                'sub_type' => $balance >= 0 ? 'success' : 'danger',
                'primary' => true,
                'icon' => 'heroicon-o-chart-bar',
            ],
        ];
    }

    // ─────────────────────────────────────────────
    // Financial Analytics (cash flow + expense breakdown)
    // ─────────────────────────────────────────────
    public function getFinancialAnalytics(): array
    {
        $pid = $this->pid();

        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->format('M');

            $invoiced = (float) Invoice::where('cde_project_id', $pid)
                ->whereYear('issue_date', $date->year)
                ->whereMonth('issue_date', $date->month)
                ->sum('total_amount');

            $received = (float) InvoicePayment::where('cde_project_id', $pid)
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $expenses = (float) Expense::where('cde_project_id', $pid)
                ->where('status', '!=', 'rejected')
                ->whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');

            $months[] = [
                'label' => $label,
                'invoiced' => $invoiced,
                'received' => $received,
                'expenses' => $expenses,
                'net' => $received - $expenses,
            ];
        }

        $categories = Expense::where('cde_project_id', $pid)
            ->where('status', '!=', 'rejected')
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as cnt')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => [
                'category' => $r->category,
                'label' => ucfirst(str_replace('_', ' ', $r->category)),
                'total' => (float) $r->total,
                'total_fmt' => CurrencyHelper::format($r->total, 0),
                'count' => $r->cnt,
            ])
            ->toArray();

        return [
            'cash_flow' => $months,
            'expense_breakdown' => $categories,
        ];
    }

    // ─────────────────────────────────────────────
    // Aging Analysis
    // ─────────────────────────────────────────────
    public function getAgingAnalysis(): array
    {
        $pid = $this->pid();
        $invoices = Invoice::where('cde_project_id', $pid)
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->where('balance_due', '>', 0)
            ->get();

        $buckets = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, '90_plus' => 0];
        $counts = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, '90_plus' => 0];

        foreach ($invoices as $inv) {
            $days = $inv->due_date ? (int) now()->diffInDays($inv->due_date, false) : 0;
            $overdueDays = max(0, -$days); // negative means overdue

            $balance = (float) $inv->balance_due;
            if ($overdueDays <= 0) {
                $buckets['current'] += $balance;
                $counts['current']++;
            } elseif ($overdueDays <= 30) {
                $buckets['1_30'] += $balance;
                $counts['1_30']++;
            } elseif ($overdueDays <= 60) {
                $buckets['31_60'] += $balance;
                $counts['31_60']++;
            } elseif ($overdueDays <= 90) {
                $buckets['61_90'] += $balance;
                $counts['61_90']++;
            } else {
                $buckets['90_plus'] += $balance;
                $counts['90_plus']++;
            }
        }

        $total = array_sum($buckets);
        return [
            'buckets' => $buckets,
            'counts' => $counts,
            'total' => $total,
            'total_fmt' => CurrencyHelper::format($total, 0),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createInvoice')
                ->label('Create Invoice')->icon('heroicon-o-document-text')->color('primary')
                ->modalWidth('5xl')
                ->schema([
                    Section::make('Invoice Information')->schema([
                        Forms\Components\TextInput::make('invoice_number')->label('Invoice #')
                            ->default(fn() => 'INV-' . str_pad((string) (Invoice::where('cde_project_id', $this->pid())->count() + 1), 5, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\Select::make('client_id')->label('Client')
                            ->options(Client::where('company_id', $this->cid())->pluck('name', 'id'))
                            ->searchable()->required()->default($this->record->client_id),
                        Forms\Components\Select::make('work_order_id')->label('Work Order')
                            ->options(WorkOrder::where('cde_project_id', $this->pid())->pluck('title', 'id'))
                            ->searchable()->nullable(),
                        Forms\Components\Select::make('status')->options(Invoice::$statuses)->required()->default('draft'),
                        Forms\Components\DatePicker::make('issue_date')->required()->default(now()),
                        Forms\Components\DatePicker::make('due_date')->required()->default(now()->addDays(14)),
                    ])->columns(2),
                    Section::make('Line Items')->schema([
                        Forms\Components\Repeater::make('items')
                            ->schema([
                                Forms\Components\TextInput::make('description')->required()->columnSpan(3),
                                Forms\Components\TextInput::make('quantity')->numeric()->default(1)->minValue(1)->columnSpan(1),
                                Forms\Components\TextInput::make('unit')->placeholder('pcs, hrs, lot…')->maxLength(20)->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0)->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $qty = max(1, intval($get('quantity') ?? 1));
                                        $set('amount', number_format($qty * floatval($state), 2, '.', ''));
                                    }),
                                Forms\Components\TextInput::make('amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0)->readOnly()->columnSpan(1),
                            ])
                            ->columns(7)
                            ->defaultItems(1)
                            ->addActionLabel('+ Add Line Item')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
                    Section::make('Totals')->schema([
                        Forms\Components\TextInput::make('subtotal')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required()->default(0),
                        Forms\Components\TextInput::make('tax_rate')->label('Tax Rate (%)')->numeric()->default(0),
                        Forms\Components\TextInput::make('tax_amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0),
                        Forms\Components\TextInput::make('total_amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required()->default(0),
                    ])->columns(4),
                    Section::make('Additional')->schema([
                        Forms\Components\Textarea::make('notes')->rows(2)->maxLength(500),
                        Forms\Components\Textarea::make('terms_and_conditions')->label('Terms & Conditions')->rows(2)->maxLength(1000),
                    ])->columns(2)->collapsed(),
                ])
                ->action(function (array $data): void {
                    $items = $data['items'] ?? [];
                    unset($data['items']);
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['created_by'] = auth()->id();
                    $data['amount_paid'] = 0;
                    $invoice = Invoice::create($data);
                    foreach ($items as $i => $item) {
                        $item['sort_order'] = $i;
                        $item['amount'] = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
                        $invoice->items()->create($item);
                    }
                    Notification::make()->title('Invoice ' . $invoice->invoice_number . ' created')->success()->send();
                }),

            Action::make('recordExpense')
                ->label('Log Expense')->icon('heroicon-o-credit-card')->color('danger')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Expense Details')->schema([
                        Forms\Components\TextInput::make('title')->required()->maxLength(255),
                        Forms\Components\TextInput::make('amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required()->default(0),
                        Forms\Components\DatePicker::make('expense_date')->required()->default(now()),
                        Forms\Components\TextInput::make('reference_number')->label('Receipt / Ref #')->maxLength(50),
                        Forms\Components\Select::make('category')->options([
                            'materials' => 'Materials',
                            'labor' => 'Labor',
                            'equipment' => 'Equipment',
                            'subcontractor' => 'Subcontractor',
                            'travel' => 'Travel',
                            'utilities' => 'Utilities',
                            'permits' => 'Permits & Fees',
                            'other' => 'Other',
                        ])->required()->default('materials'),
                        Forms\Components\Select::make('status')->options(Expense::$statuses)->required()->default('pending'),
                        Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                    ])->columns(2),
                ])
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['recorded_by'] = auth()->id();
                    Expense::create($data);
                    Notification::make()->title('Expense logged')->success()->send();
                }),
        ];
    }

    // ─────────────────────────────────────────────
    // Table routing by tab
    // ─────────────────────────────────────────────
    public function table(Table $table): Table
    {
        if ($this->activeTab === 'receipts') {
            return $this->receiptsTable($table->query(
                InvoicePayment::query()->where('cde_project_id', $this->pid())->with(['invoice.client', 'recorder'])
            ));
        }
        if ($this->activeTab === 'expenses') {
            return $this->expensesTable($table->query(
                Expense::query()->where('cde_project_id', $this->pid())->with(['recorder'])
            ));
        }
        return $this->invoicesTable($table->query(
            Invoice::query()->where('cde_project_id', $this->pid())->with(['client', 'creator', 'items'])
        ));
    }

    /* ══════════════════════════════════════════════════════════════
       INVOICES TABLE — with row-click
       ══════════════════════════════════════════════════════════════ */
    private function invoicesTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice #')->searchable()->sortable()->weight('bold')->toggleable()
                    ->icon('heroicon-o-document-text')->copyable(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'paid' => 'success', 'partially_paid' => 'info', 'sent' => 'primary', 'draft' => 'gray', 'overdue' => 'danger', 'cancelled' => 'warning', default => 'gray'})->sortable()
                    ->formatStateUsing(fn($state) => Invoice::$statuses[$state] ?? $state),
                Tables\Columns\TextColumn::make('items_count')->label('Items')->counts('items')->badge()->color('gray')->toggleable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Total')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('amount_paid')->label('Paid')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable()
                    ->color(fn(Invoice $record) => $record->amount_paid > 0 && $record->amount_paid < $record->total_amount ? 'warning' : null),
                Tables\Columns\TextColumn::make('balance_due')->label('Balance')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->toggleable()
                    ->color(fn(Invoice $record) => $record->balance_due > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('issue_date')->date('M d, Y')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('due_date')->date('M d, Y')->sortable()->toggleable()
                    ->color(fn(Invoice $record) => $record->due_date && \Carbon\Carbon::parse($record->due_date)->isPast() && !in_array($record->status, ['paid', 'cancelled']) ? 'danger' : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Invoice::$statuses)->multiple(),
                Tables\Filters\Filter::make('overdue')->label('Overdue Only')
                    ->query(fn($q) => $q->where('due_date', '<', now())->whereNotIn('status', ['paid', 'cancelled']))->toggle(),
            ])
            ->recordAction('viewDetail')
            ->recordActions([
                /* ── View Detail (row-click target) ── */
                \Filament\Actions\Action::make('viewDetail')
                    ->label('View')->icon('heroicon-o-eye')->color('gray')
                    ->modalWidth('4xl')
                    ->modalHeading(fn(Invoice $record) => 'Invoice ' . $record->invoice_number)
                    ->schema(fn(Invoice $record) => [
                        Forms\Components\Placeholder::make('info_bar')
                            ->content(fn() => new HtmlString($this->invoiceInfoBarHtml($record)))
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('financials')
                            ->content(fn() => new HtmlString($this->invoiceFinancialsHtml($record)))
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('line_items')->label('Line Items (' . $record->items->count() . ')')
                            ->content(fn() => $this->invoiceItemsHtml($record))
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('notes_display')->label('Notes')
                            ->content($record->notes ?: '—')->columnSpanFull(),
                    ])
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('recordPayment')
                        ->label('Record Payment')->icon('heroicon-o-banknotes')->color('success')
                        ->visible(fn(Invoice $record) => !in_array($record->status, ['paid', 'cancelled']) && $record->balance_due > 0)
                        ->schema([
                            Forms\Components\TextInput::make('amount')->label('Amount Received')
                                ->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required()->default(fn(Invoice $record) => $record->balance_due),
                            Forms\Components\Select::make('payment_method')->options([
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                                'credit_card' => 'Credit Card',
                                'check' => 'Check',
                                'mobile_money' => 'Mobile Money',
                            ])->required()->default('bank_transfer'),
                            Forms\Components\TextInput::make('reference')->label('Reference #'),
                            Forms\Components\DatePicker::make('payment_date')->required()->default(now()),
                            Forms\Components\Textarea::make('notes')->rows(2),
                        ])
                        ->action(function (array $data, Invoice $record): void {
                            $data['invoice_id'] = $record->id;
                            $data['cde_project_id'] = $this->pid();
                            $data['recorded_by'] = auth()->id();
                            InvoicePayment::create($data);
                            $newPaid = $record->amount_paid + $data['amount'];
                            $newStatus = $newPaid >= $record->total_amount ? 'paid' : 'partially_paid';
                            $record->update(['amount_paid' => $newPaid, 'status' => $newStatus]);
                            Notification::make()->title('Payment of ' . CurrencyHelper::format($data['amount']) . ' recorded')->success()->send();
                        }),

                    \Filament\Actions\Action::make('sendReminder')
                        ->label('Send Reminder')->icon('heroicon-o-bell-alert')->color('warning')
                        ->visible(fn(Invoice $record) => !in_array($record->status, ['paid', 'cancelled', 'draft']) && $record->balance_due > 0)
                        ->requiresConfirmation()
                        ->modalHeading(fn(Invoice $record) => 'Send Payment Reminder — ' . $record->invoice_number)
                        ->modalDescription(fn(Invoice $record) => 'Send a payment reminder notification for ' . CurrencyHelper::format($record->balance_due) . ' outstanding.' .
                            ($record->reminder_count > 0 ? "\n\nPrevious reminders sent: " . $record->reminder_count : ''))
                        ->action(function (Invoice $record): void {
                            $record->update([
                                'reminder_sent_at' => now(),
                                'reminder_count' => $record->reminder_count + 1,
                                'status' => $record->due_date && $record->due_date->isPast() ? 'overdue' : $record->status,
                            ]);
                            \App\Models\Notification::create([
                                'user_id' => $record->created_by ?? auth()->id(),
                                'title' => 'Payment Reminder Sent',
                                'message' => 'Reminder #' . $record->reminder_count . ' sent for Invoice ' . $record->invoice_number . ' — ' . CurrencyHelper::format($record->balance_due) . ' outstanding.',
                                'type' => 'invoice_reminder',
                                'data' => ['invoice_id' => $record->id, 'project_id' => $this->pid()],
                            ]);
                            Notification::make()->title('Reminder #' . $record->reminder_count . ' sent for ' . $record->invoice_number)->success()->send();
                        }),

                    \Filament\Actions\Action::make('printInvoice')
                        ->label('Print')->icon('heroicon-o-printer')->color('gray')
                        ->url(fn(Invoice $record) => route('print.invoice', $record), shouldOpenInNewTab: true),

                    \Filament\Actions\Action::make('markOverdue')
                        ->label('Mark Overdue')->icon('heroicon-o-clock')->color('danger')
                        ->visible(fn(Invoice $record) => $record->due_date && \Carbon\Carbon::parse($record->due_date)->isPast() && !in_array($record->status, ['paid', 'overdue', 'cancelled']))
                        ->requiresConfirmation()
                        ->action(function (Invoice $record): void {
                            $record->update(['status' => 'overdue']);
                            Notification::make()->title('Invoice marked as overdue')->warning()->send();
                        }),

                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->color('info')
                        ->url(fn(Invoice $record) => route('filament.app.resources.invoices.edit', $record)),

                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(Invoice $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('invoices', fn() => Invoice::query()->where('cde_project_id', $this->pid())->with(['client', 'creator']), [
                    'invoice_number' => 'Invoice #',
                    'client.name' => 'Client',
                    'status' => 'Status',
                    'issue_date' => 'Issue Date',
                    'due_date' => 'Due Date',
                    'subtotal' => 'Subtotal',
                    'tax_amount' => 'Tax',
                    'total_amount' => 'Total',
                    'amount_paid' => 'Paid',
                    'balance_due' => 'Balance',
                    'creator.name' => 'Created By',
                ]),
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkMarkOverdue')->label('Mark Overdue')->icon('heroicon-o-clock')->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $r) {
                                if (!in_array($r->status, ['paid', 'cancelled']))
                                    $r->update(['status' => 'overdue']);
                            }
                            Notification::make()->title($records->count() . ' invoice(s) marked overdue')->warning()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Invoices')
            ->emptyStateDescription('Create invoices with line items to bill your clients.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->striped()->paginated([10, 25, 50]);
    }

    /* Helper: Invoice info bar HTML */
    private function invoiceInfoBarHtml(Invoice $record): string
    {
        $fields = [
            ['Invoice', $record->invoice_number],
            ['Status', Invoice::$statuses[$record->status] ?? $record->status],
            ['Client', $record->client?->name ?? '—'],
            ['Issued', $record->issue_date?->format('M d, Y') ?? '—'],
            ['Due', $record->due_date?->format('M d, Y') ?? '—'],
            ['By', $record->creator?->name ?? '—'],
        ];
        $cells = collect($fields)->map(
            fn($c) =>
            '<div style="display:flex;gap:3px;align-items:baseline;">' .
            '<span style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.04em;color:#9ca3af;">' . $c[0] . '</span>' .
            '<span style="font-size:11px;font-weight:600;color:#1f2937;">' . e($c[1]) . '</span></div>'
        )->join('');
        return '<div style="display:flex;flex-wrap:wrap;gap:8px 16px;padding:6px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;">' . $cells . '</div>';
    }

    /* Helper: Invoice financials HTML */
    private function invoiceFinancialsHtml(Invoice $record): string
    {
        $paidPct = $record->total_amount > 0 ? (int) round(($record->amount_paid / $record->total_amount) * 100) : 0;
        $barColor = $paidPct >= 100 ? '#059669' : ($paidPct >= 50 ? '#3b82f6' : '#6366f1');

        $cards = [
            ['Total', CurrencyHelper::format($record->total_amount), '#334155'],
            ['Paid', CurrencyHelper::format($record->amount_paid), '#059669'],
            ['Balance Due', CurrencyHelper::format($record->balance_due), $record->balance_due > 0 ? '#dc2626' : '#059669'],
        ];
        $html = '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;margin-bottom:0.5rem;">';
        foreach ($cards as $c) {
            $html .= '<div style="text-align:center;padding:0.5rem;border-radius:0.5rem;border:1px solid #e5e7eb;background:#f9fafb;">' .
                '<div style="font-size:9px;font-weight:700;text-transform:uppercase;color:#6b7280;">' . $c[0] . '</div>' .
                '<div style="font-size:1.1rem;font-weight:800;color:' . $c[2] . ';">' . $c[1] . '</div></div>';
        }
        $html .= '</div>';

        // Progress bar
        $html .= '<div style="display:flex;justify-content:space-between;font-size:10px;font-weight:600;color:#6b7280;margin-bottom:3px;">' .
            '<span>Payment Progress</span><span style="color:' . $barColor . ';">' . $paidPct . '%</span></div>' .
            '<div style="height:6px;background:#e2e8f0;border-radius:3px;overflow:hidden;">' .
            '<div style="height:100%;width:' . min($paidPct, 100) . '%;background:' . $barColor . ';border-radius:3px;"></div></div>';

        return $html;
    }

    /* Helper: Invoice line items HTML */
    private function invoiceItemsHtml(Invoice $record): HtmlString
    {
        if ($record->items->isEmpty()) {
            return new HtmlString('<em style="color:#9ca3af;">No line items</em>');
        }
        $rows = $record->items->map(
            fn($it) =>
            '<tr style="border-bottom:1px solid #f3f4f6;">' .
            '<td style="padding:6px;">' . e($it->description) . '</td>' .
            '<td style="text-align:center;padding:6px;">' . $it->quantity . '</td>' .
            '<td style="text-align:center;padding:6px;">' . ($it->unit ?? '—') . '</td>' .
            '<td style="text-align:right;padding:6px;">' . CurrencyHelper::format($it->unit_price) . '</td>' .
            '<td style="text-align:right;padding:6px;font-weight:600;">' . CurrencyHelper::format($it->amount) . '</td></tr>'
        )->join('');

        return new HtmlString(
            '<table style="width:100%;border-collapse:collapse;font-size:12px;"><thead><tr style="border-bottom:2px solid #e5e7eb;">' .
            '<th style="text-align:left;padding:6px;">Description</th><th style="text-align:center;padding:6px;">Qty</th><th style="text-align:center;padding:6px;">Unit</th><th style="text-align:right;padding:6px;">Price</th><th style="text-align:right;padding:6px;">Amount</th>' .
            '</tr></thead><tbody>' . $rows . '</tbody></table>'
        );
    }

    /* ══════════════════════════════════════════════════════════════
       RECEIPTS TABLE — with row-click
       ══════════════════════════════════════════════════════════════ */
    private function receiptsTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')->label('Invoice #')->searchable()->sortable()->weight('bold')->toggleable()
                    ->icon('heroicon-o-document-text'),
                Tables\Columns\TextColumn::make('invoice.client.name')->label('Client')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('amount')->label('Amount')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->weight('bold')->color('success')->toggleable(),
                Tables\Columns\TextColumn::make('payment_method')->label('Method')->badge()->color('info')->toggleable()
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('reference')->label('Ref #')->searchable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('payment_date')->date('M d, Y')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('recorder.name')->label('Recorded By')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('payment_date', 'desc')
            ->recordAction('viewReceipt')
            ->recordActions([
                \Filament\Actions\Action::make('viewReceipt')
                    ->label('View')->icon('heroicon-o-eye')->color('gray')
                    ->modalWidth('lg')
                    ->modalHeading(fn(InvoicePayment $record) => 'Receipt — ' . ($record->invoice?->invoice_number ?? 'Payment'))
                    ->schema(fn(InvoicePayment $record) => [
                        Forms\Components\Placeholder::make('date')->label('Payment Date')
                            ->content($record->payment_date?->format('M d, Y') ?? '—'),
                        Forms\Components\Placeholder::make('amt')->label('Amount Paid')
                            ->content(fn() => new HtmlString('<span style="font-size:20px;font-weight:800;color:#059669;">' . CurrencyHelper::format($record->amount) . '</span>')),
                        Forms\Components\Placeholder::make('method')->label('Method')
                            ->content(ucwords(str_replace('_', ' ', $record->payment_method ?? '—'))),
                        Forms\Components\Placeholder::make('ref')->label('Reference')
                            ->content($record->reference ?? '—'),
                        Forms\Components\Placeholder::make('by')->label('Recorded By')
                            ->content($record->recorder?->name ?? '—'),
                        Forms\Components\Placeholder::make('note')->label('Notes')
                            ->content($record->notes ?? '—')->columnSpanFull(),
                    ])
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('printReceipt')
                        ->label('Print')->icon('heroicon-o-printer')->color('gray')
                        ->url(fn(InvoicePayment $record) => route('print.receipt', $record), shouldOpenInNewTab: true),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('receipts', fn() => InvoicePayment::query()->where('cde_project_id', $this->pid())->with(['invoice.client', 'recorder']), [
                    'invoice.invoice_number' => 'Invoice #',
                    'invoice.client.name' => 'Client',
                    'amount' => 'Amount',
                    'payment_method' => 'Method',
                    'reference' => 'Reference',
                    'payment_date' => 'Date',
                    'recorder.name' => 'Recorded By',
                    'notes' => 'Notes',
                ]),
            ])
            ->emptyStateHeading('No Receipts')
            ->emptyStateDescription('Payments recorded against invoices will appear here.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->striped()->paginated([10, 25, 50]);
    }

    /* ══════════════════════════════════════════════════════════════
       EXPENSES TABLE — with row-click
       ══════════════════════════════════════════════════════════════ */
    private function expensesTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->weight('bold')->tooltip(fn(Expense $record) => $record->title)->toggleable(),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray')->toggleable()
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('amount')->formatStateUsing(CurrencyHelper::formatter(0))->sortable()->color('danger')->weight('bold')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'paid' => 'success', 'approved' => 'info', 'pending' => 'warning', 'rejected' => 'danger', default => 'gray'})->sortable()
                    ->formatStateUsing(fn($state) => Expense::$statuses[$state] ?? $state),
                Tables\Columns\TextColumn::make('expense_date')->date('M d, Y')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('reference_number')->label('Ref #')->searchable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('recorder.name')->label('Recorded By')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('expense_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Expense::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('category')->options([
                    'materials' => 'Materials',
                    'labor' => 'Labor',
                    'equipment' => 'Equipment',
                    'subcontractor' => 'Subcontractor',
                    'travel' => 'Travel',
                    'utilities' => 'Utilities',
                    'permits' => 'Permits & Fees',
                    'other' => 'Other',
                ]),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('approve')
                        ->icon('heroicon-o-check')->color('success')
                        ->visible(fn(Expense $record) => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->action(function (Expense $record) {
                            $record->update(['status' => 'approved']);
                            Notification::make()->title('Expense approved')->success()->send();
                        }),
                    \Filament\Actions\Action::make('reject')
                        ->icon('heroicon-o-x-mark')->color('danger')
                        ->visible(fn(Expense $record) => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->action(function (Expense $record) {
                            $record->update(['status' => 'rejected']);
                            Notification::make()->title('Expense rejected')->warning()->send();
                        }),
                    \Filament\Actions\Action::make('markPaid')
                        ->label('Mark Paid')->icon('heroicon-o-currency-dollar')->color('success')
                        ->visible(fn(Expense $record) => in_array($record->status, ['pending', 'approved']))
                        ->action(function (Expense $record) {
                            $record->update(['status' => 'paid']);
                            Notification::make()->title('Expense marked as paid')->success()->send();
                        }),
                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('3xl')
                        ->schema([
                            Section::make('Expense Details')->schema([
                                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                                Forms\Components\TextInput::make('amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->required(),
                                Forms\Components\DatePicker::make('expense_date')->required(),
                                Forms\Components\TextInput::make('reference_number')->label('Receipt / Ref #')->maxLength(50),
                                Forms\Components\Select::make('category')->options([
                                    'materials' => 'Materials',
                                    'labor' => 'Labor',
                                    'equipment' => 'Equipment',
                                    'subcontractor' => 'Subcontractor',
                                    'travel' => 'Travel',
                                    'utilities' => 'Utilities',
                                    'permits' => 'Permits & Fees',
                                    'other' => 'Other',
                                ])->required(),
                                Forms\Components\Select::make('status')->options(Expense::$statuses)->required(),
                                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                            ])->columns(2),
                        ])
                        ->fillForm(fn(Expense $record) => $record->toArray())
                        ->action(function (array $data, Expense $record) {
                            $record->update($data);
                            Notification::make()->title('Expense updated')->success()->send();
                        }),
                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(Expense $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('expenses', fn() => Expense::query()->where('cde_project_id', $this->pid())->with(['recorder']), [
                    'expense_number' => 'Expense #',
                    'category' => 'Category',
                    'description' => 'Description',
                    'amount' => 'Amount',
                    'status' => 'Status',
                    'expense_date' => 'Date',
                    'vendor_name' => 'Vendor',
                    'recorder.name' => 'Recorded By',
                    'created_at' => 'Created At',
                ]),
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkApprove')->label('Approve')->icon('heroicon-o-check')->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $r) {
                                if ($r->status === 'pending')
                                    $r->update(['status' => 'approved']);
                            }
                            Notification::make()->title($records->count() . ' expense(s) approved')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Expenses')
            ->emptyStateDescription('Log project expenses to track costs.')
            ->emptyStateIcon('heroicon-o-credit-card')
            ->striped()->paginated([10, 25, 50]);
    }
}
