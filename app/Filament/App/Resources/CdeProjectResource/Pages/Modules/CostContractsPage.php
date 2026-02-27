<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Certificate;
use App\Models\Contract;
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

    public function getStats(): array
    {
        $pid = $this->pid();
        $base = Contract::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $active = (clone $base)->where('status', 'active')->count();
        $origVal = (clone $base)->sum('original_value');
        $revisedVal = (clone $base)->sum('revised_value');
        $paid = (clone $base)->sum('amount_paid');

        return [
            [
                'label' => 'Total Contracts',
                'value' => $total,
                'sub' => $active . ' active',
                'sub_type' => 'info',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
            ],
            [
                'label' => 'Original Value',
                'value' => CurrencyHelper::format($origVal, 0),
                'sub' => 'Contract sum',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#eef2ff'
            ],
            [
                'label' => 'Revised Value',
                'value' => CurrencyHelper::format($revisedVal, 0),
                'sub' => $revisedVal > $origVal ? 'Exceeded original' : 'Within budget',
                'sub_type' => $revisedVal > $origVal ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
            [
                'label' => 'Amount Paid',
                'value' => CurrencyHelper::format($paid, 0),
                'sub' => $revisedVal > 0 ? round(($paid / $revisedVal) * 100) . '% of revised' : 'No contracts',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

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
                Forms\Components\TextInput::make('original_value')->label('Original Value')->numeric()->prefix('$')->required()->default(0),
                Forms\Components\TextInput::make('revised_value')->label('Revised Value')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('amount_paid')->label('Amount Paid')->numeric()->prefix('$')->default(0)->visible(!$isCreate),
                Forms\Components\TextInput::make('retainage_percent')->label('Retainage (%)')->numeric()->suffix('%')->default(0)
                    ->helperText('Percentage withheld from each payment'),
                Forms\Components\TextInput::make('retainage_held')->label('Retainage Held')->numeric()->prefix('$')->default(0)->visible(!$isCreate),
                Forms\Components\TextInput::make('retainage_released')->label('Retainage Released')->numeric()->prefix('$')->default(0)->visible(!$isCreate),
            ])->columns(2),
            Section::make('Scope & Description')->schema([
                Forms\Components\RichEditor::make('description')->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])->columnSpanFull(),
                Forms\Components\Textarea::make('scope_of_work')->label('Scope of Work')->rows(3)->columnSpanFull(),
            ])->collapsed(!$isCreate),
        ];
    }

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

    public function table(Table $table): Table
    {
        return $table
            ->query(Contract::query()->where('cde_project_id', $this->pid())->with(['vendor', 'creator']))
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')->label('Contract #')->searchable()->sortable()->weight('bold')->toggleable()
                    ->icon('heroicon-o-document-text')->copyable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->tooltip(fn(Contract $record) => $record->title)->toggleable(),
                Tables\Columns\TextColumn::make('vendor.name')->label('Vendor')->placeholder('—')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('type')->badge()->color('info')->toggleable(),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'active' => 'success', 'completed' => 'primary', 'draft' => 'gray', 'terminated' => 'danger', 'suspended' => 'warning', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('original_value')->label('Original')->money('USD')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('revised_value')->label('Revised')->money('USD')->sortable()->toggleable()
                    ->color(fn(Contract $record) => ($record->revised_value ?? 0) > ($record->original_value ?? 0) ? 'danger' : null),
                Tables\Columns\TextColumn::make('amount_paid')->label('Paid')->money('USD')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('retainage_held')->label('Ret. Held')->money('USD')->sortable()->toggleable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_progress')->label('% Paid')->toggleable()
                    ->state(fn(Contract $record) => $record->revised_value > 0 ? round(($record->amount_paid / $record->revised_value) * 100) . '%' : '—')
                    ->toggleable(),
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
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('viewDetail')
                        ->label('View')->icon('heroicon-o-eye')->color('gray')
                        ->modalWidth('3xl')
                        ->modalHeading(fn(Contract $record) => $record->contract_number . ' — ' . $record->title)
                        ->schema(fn(Contract $record) => [
                            Forms\Components\Placeholder::make('vendor')->label('Vendor')
                                ->content($record->vendor?->name ?? '—'),
                            Forms\Components\Placeholder::make('type')->label('Type')
                                ->content(ucfirst($record->type ?? '—')),
                            Forms\Components\Placeholder::make('status_display')->label('Status')
                                ->content(Contract::$statuses[$record->status] ?? $record->status),
                            Forms\Components\Placeholder::make('dates')->label('Duration')
                                ->content(($record->start_date?->format('M d, Y') ?? '—') . ' → ' . ($record->end_date?->format('M d, Y') ?? '—')),
                            Forms\Components\Placeholder::make('financials')->label('Financials')
                                ->content(fn() => 'Original: ' . CurrencyHelper::format($record->original_value) .
                                    ' | Revised: ' . CurrencyHelper::format($record->revised_value) .
                                    ' | Paid: ' . CurrencyHelper::format($record->amount_paid) .
                                    ' (' . ($record->revised_value > 0 ? round(($record->amount_paid / $record->revised_value) * 100) : 0) . '%)')
                                ->columnSpanFull(),
                            Forms\Components\Placeholder::make('retainage')->label('Retainage')
                                ->content(fn() =>
                                    'Rate: ' . ($record->retainage_percent ?? 0) . '% | ' .
                                    'Held: ' . CurrencyHelper::format($record->retainage_held ?? 0) . ' | ' .
                                    'Released: ' . CurrencyHelper::format($record->retainage_released ?? 0) . ' | ' .
                                    'Remaining: ' . CurrencyHelper::format(($record->retainage_held ?? 0) - ($record->retainage_released ?? 0)))
                                ->columnSpanFull(),
                            Forms\Components\Placeholder::make('scope')->label('Scope of Work')
                                ->content($record->scope_of_work ?: '—')
                                ->columnSpanFull(),
                        ])
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    \Filament\Actions\Action::make('recordPayment')
                        ->label('Payment')->icon('heroicon-o-banknotes')->color('success')
                        ->schema([
                            Forms\Components\TextInput::make('payment_amount')->label('Payment Amount')
                                ->numeric()->prefix('$')->required(),
                            Forms\Components\Textarea::make('payment_note')->label('Note')->rows(2),
                        ])
                        ->fillForm(fn(Contract $record) => ['payment_amount' => 0])
                        ->action(function (array $data, Contract $record): void {
                            $newPaid = ($record->amount_paid ?? 0) + $data['payment_amount'];
                            $notes = $record->description ? $record->description . "\n" : '';
                            $notes .= '[Payment ' . now()->format('M d') . ' — $' . number_format($data['payment_amount'], 2) . ']';
                            if (!empty($data['payment_note']))
                                $notes .= ' ' . $data['payment_note'];
                            $record->update(['amount_paid' => $newPaid, 'description' => $notes]);
                            Notification::make()->title('Payment of ' . CurrencyHelper::format($data['payment_amount']) . ' recorded. Total paid: ' . CurrencyHelper::format($newPaid))->success()->send();
                        }),

                    \Filament\Actions\Action::make('addVariation')
                        ->label('Variation')->icon('heroicon-o-plus-circle')->color('warning')
                        ->modalHeading(fn(Contract $record) => 'Add Variation — ' . $record->contract_number)
                        ->schema([
                            Forms\Components\TextInput::make('variation_amount')->label('Variation Amount')
                                ->numeric()->prefix('$')->required()
                                ->helperText('Positive = addition, negative = deduction'),
                            Forms\Components\Textarea::make('reason')->label('Reason for Variation')->rows(2)->required(),
                        ])
                        ->action(function (array $data, Contract $record): void {
                            $newRevised = ($record->revised_value ?? $record->original_value ?? 0) + $data['variation_amount'];
                            $notes = $record->description ? $record->description . "\n" : '';
                            $notes .= '[Variation ' . now()->format('M d') . ' — ' . ($data['variation_amount'] >= 0 ? '+' : '') . '$' . number_format($data['variation_amount'], 2) . '] ' . $data['reason'];
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
                ->content(fn() => new \Illuminate\Support\HtmlString($summary . $html))
                ->columnSpanFull(),
        ];
    }
}
