<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
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

class CostContractsPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

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
                Forms\Components\TextInput::make('retainage_percent')->label('Retainage (%)')->numeric()->suffix('%')->default(0),
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
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Contract::query()->where('cde_project_id', $this->pid())->with(['vendor', 'creator']))
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')->label('Contract #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-document-text')->copyable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->tooltip(fn(Contract $record) => $record->title),
                Tables\Columns\TextColumn::make('vendor.name')->label('Vendor')->placeholder('—')->searchable(),
                Tables\Columns\TextColumn::make('type')->badge()->color('info'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'active' => 'success', 'completed' => 'primary', 'draft' => 'gray', 'terminated' => 'danger', 'suspended' => 'warning', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('original_value')->label('Original')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('revised_value')->label('Revised')->money('USD')->sortable()
                    ->color(fn(Contract $record) => ($record->revised_value ?? 0) > ($record->original_value ?? 0) ? 'danger' : null),
                Tables\Columns\TextColumn::make('amount_paid')->label('Paid')->money('USD')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('payment_progress')->label('% Paid')
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
}
