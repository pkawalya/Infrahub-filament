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

    public function getStats(): array
    {
        $pid = $this->pid();

        // Single aggregate query instead of 5 separate queries
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

        return [
            [
                'label' => 'Total Invoiced',
                'value' => CurrencyHelper::format($invoiced, 0),
                'sub' => $overdue > 0 ? $overdue . ' overdue' : 'All on track',
                'sub_type' => $overdue > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>',
                'icon_bg' => '#eef2ff'
            ],
            [
                'label' => 'Amount Received',
                'value' => CurrencyHelper::format($received, 0),
                'sub' => 'Collected payments',
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#10b981" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Total Expenses',
                'value' => CurrencyHelper::format($expenses, 0),
                'sub' => 'Project costs',
                'sub_type' => 'danger',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'Net Balance',
                'value' => CurrencyHelper::format($balance, 0),
                'sub' => 'Revenue - Expenses',
                'sub_type' => $balance >= 0 ? 'success' : 'danger',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>'
            ],
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
                                Forms\Components\TextInput::make('unit_price')->numeric()->prefix('$')->default(0)->columnSpan(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $qty = max(1, intval($get('quantity') ?? 1));
                                        $set('amount', number_format($qty * floatval($state), 2, '.', ''));
                                    }),
                                Forms\Components\TextInput::make('amount')->numeric()->prefix('$')->default(0)->readOnly()->columnSpan(1),
                            ])
                            ->columns(7)
                            ->defaultItems(1)
                            ->addActionLabel('+ Add Line Item')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
                    Section::make('Totals')->schema([
                        Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$')->required()->default(0),
                        Forms\Components\TextInput::make('tax_rate')->label('Tax Rate (%)')->numeric()->default(0),
                        Forms\Components\TextInput::make('tax_amount')->numeric()->prefix('$')->default(0),
                        Forms\Components\TextInput::make('total_amount')->numeric()->prefix('$')->required()->default(0),
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
                        Forms\Components\TextInput::make('amount')->numeric()->prefix('$')->required()->default(0),
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
       INVOICES TABLE
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
                Tables\Columns\TextColumn::make('total_amount')->label('Total')->money('USD')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('amount_paid')->label('Paid')->money('USD')->sortable()->toggleable()
                    ->color(fn(Invoice $record) => $record->amount_paid > 0 && $record->amount_paid < $record->total_amount ? 'warning' : null),
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
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    // View invoice detail
                    \Filament\Actions\Action::make('viewDetail')
                        ->label('View')->icon('heroicon-o-eye')->color('gray')
                        ->modalWidth('4xl')
                        ->modalHeading(fn(Invoice $record) => 'Invoice ' . $record->invoice_number)
                        ->schema(fn(Invoice $record) => [
                            Forms\Components\Placeholder::make('client')->label('Client')
                                ->content($record->client?->name ?? '—'),
                            Forms\Components\Placeholder::make('status_display')->label('Status')
                                ->content(Invoice::$statuses[$record->status] ?? $record->status),
                            Forms\Components\Placeholder::make('dates')->label('Issue → Due')
                                ->content(($record->issue_date?->format('M d, Y') ?? '—') . ' → ' . ($record->due_date?->format('M d, Y') ?? '—')),
                            Forms\Components\Placeholder::make('amounts')->label('Financial Summary')
                                ->content(fn() => new HtmlString(
                                    '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">' .
                                    '<div><strong style="color:#6b7280;font-size:11px;">TOTAL</strong><br><span style="font-size:18px;font-weight:800;">' . CurrencyHelper::format($record->total_amount) . '</span></div>' .
                                    '<div><strong style="color:#6b7280;font-size:11px;">PAID</strong><br><span style="font-size:18px;font-weight:800;color:#059669;">' . CurrencyHelper::format($record->amount_paid) . '</span></div>' .
                                    '<div><strong style="color:#6b7280;font-size:11px;">BALANCE DUE</strong><br><span style="font-size:18px;font-weight:800;color:' . ($record->balance_due > 0 ? '#dc2626' : '#059669') . ';">' . CurrencyHelper::format($record->balance_due) . '</span></div>' .
                                    '</div>'
                                ))->columnSpanFull(),
                            Forms\Components\Placeholder::make('line_items')->label('Line Items (' . $record->items->count() . ')')
                                ->content(
                                    fn() => $record->items->count() > 0
                                    ? new HtmlString(
                                        '<table style="width:100%;border-collapse:collapse;font-size:12px;"><thead><tr style="border-bottom:2px solid #e5e7eb;">' .
                                        '<th style="text-align:left;padding:6px;">Description</th><th style="text-align:center;padding:6px;">Qty</th><th style="text-align:center;padding:6px;">Unit</th><th style="text-align:right;padding:6px;">Price</th><th style="text-align:right;padding:6px;">Amount</th>' .
                                        '</tr></thead><tbody>' .
                                        $record->items->map(fn($it) => '<tr style="border-bottom:1px solid #f3f4f6;"><td style="padding:6px;">' . e($it->description) . '</td><td style="text-align:center;padding:6px;">' . $it->quantity . '</td><td style="text-align:center;padding:6px;">' . ($it->unit ?? '—') . '</td><td style="text-align:right;padding:6px;">' . CurrencyHelper::format($it->unit_price) . '</td><td style="text-align:right;padding:6px;font-weight:600;">' . CurrencyHelper::format($it->amount) . '</td></tr>')->join('') .
                                        '</tbody></table>'
                                    )
                                    : new HtmlString('<em style="color:#9ca3af;">No line items</em>')
                                )->columnSpanFull(),
                            Forms\Components\Placeholder::make('notes_display')->label('Notes')
                                ->content($record->notes ?: '—')->columnSpanFull(),
                        ])
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    // Record payment
                    \Filament\Actions\Action::make('recordPayment')
                        ->label('Record Payment')->icon('heroicon-o-banknotes')->color('success')
                        ->visible(fn(Invoice $record) => !in_array($record->status, ['paid', 'cancelled']) && $record->balance_due > 0)
                        ->schema([
                            Forms\Components\TextInput::make('amount')->label('Amount Received')
                                ->numeric()->prefix('$')->required()->default(fn(Invoice $record) => $record->balance_due),
                            Forms\Components\Select::make('payment_method')->options([
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                                'credit_card' => 'Credit Card',
                                'check' => 'Check',
                                'mobile_money' => 'Mobile Money'
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

                    // Send reminder
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

                            // Create an in-app notification for the invoice creator
                            \App\Models\Notification::create([
                                'user_id' => $record->created_by ?? auth()->id(),
                                'title' => 'Payment Reminder Sent',
                                'message' => 'Reminder #' . $record->reminder_count . ' sent for Invoice ' . $record->invoice_number . ' — ' . CurrencyHelper::format($record->balance_due) . ' outstanding.',
                                'type' => 'invoice_reminder',
                                'data' => ['invoice_id' => $record->id, 'project_id' => $this->pid()],
                            ]);

                            Notification::make()->title('Reminder #' . $record->reminder_count . ' sent for ' . $record->invoice_number)->success()->send();
                        }),

                    // Print invoice
                    \Filament\Actions\Action::make('printInvoice')
                        ->label('Print')->icon('heroicon-o-printer')->color('gray')
                        ->url(fn(Invoice $record) => route('print.invoice', $record), shouldOpenInNewTab: true),

                    // Mark overdue
                    \Filament\Actions\Action::make('markOverdue')
                        ->label('Mark Overdue')->icon('heroicon-o-clock')->color('danger')
                        ->visible(fn(Invoice $record) => $record->due_date && \Carbon\Carbon::parse($record->due_date)->isPast() && !in_array($record->status, ['paid', 'overdue', 'cancelled']))
                        ->requiresConfirmation()
                        ->action(function (Invoice $record): void {
                            $record->update(['status' => 'overdue']);
                            Notification::make()->title('Invoice marked as overdue')->warning()->send();
                        }),

                    // Edit
                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->color('info')
                        ->url(fn(Invoice $record) => route('filament.app.resources.invoices.edit', $record)),

                    // Delete
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
                                if (!in_array($r->status, ['paid', 'cancelled'])) {
                                    $r->update(['status' => 'overdue']);
                                }
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

    /* ══════════════════════════════════════════════════════════════
       RECEIPTS TABLE
       ══════════════════════════════════════════════════════════════ */
    private function receiptsTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')->label('Invoice #')->searchable()->sortable()->weight('bold')->toggleable()
                    ->icon('heroicon-o-document-text'),
                Tables\Columns\TextColumn::make('invoice.client.name')->label('Client')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('amount')->label('Amount')->money('USD')->sortable()->weight('bold')->color('success')->toggleable(),
                Tables\Columns\TextColumn::make('payment_method')->label('Method')->badge()->color('info')->toggleable()
                    ->formatStateUsing(fn($state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('reference')->label('Ref #')->searchable()->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('payment_date')->date('M d, Y')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('recorder.name')->label('Recorded By')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('payment_date', 'desc')
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
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
       EXPENSES TABLE
       ══════════════════════════════════════════════════════════════ */
    private function expensesTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->weight('bold')->tooltip(fn(Expense $record) => $record->title)->toggleable(),
                Tables\Columns\TextColumn::make('category')->badge()->color('gray')->toggleable()
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('amount')->money('USD')->sortable()->color('danger')->weight('bold')->toggleable(),
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
                                Forms\Components\TextInput::make('amount')->numeric()->prefix('$')->required(),
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
