<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Contract;
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
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $title = 'Cost & Contract Management';
    protected string $view = 'filament.app.pages.modules.cost-contracts';

    public function getStats(): array
    {
        $pid = $this->record->id;
        $total = Contract::where('cde_project_id', $pid)->count();
        $totalValue = Contract::where('cde_project_id', $pid)->sum('original_value');
        $revisedValue = Contract::where('cde_project_id', $pid)->sum('revised_value');
        $active = Contract::where('cde_project_id', $pid)->where('status', 'active')->count();
        $variance = $revisedValue > 0 ? $revisedValue - $totalValue : 0;

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
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => 'Contract sum',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Variance',
                'value' => CurrencyHelper::format(abs($variance), 0),
                'sub' => $variance >= 0 ? 'Over budget' : 'Under budget',
                'sub_type' => $variance > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        $companyId = $this->record->company_id;
        $projectId = $this->record->id;

        return [
            Action::make('createContract')
                ->label('New Contract')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Contract Information')->schema([
                        Forms\Components\TextInput::make('contract_number')
                            ->default(fn() => 'CON-' . str_pad((string) (Contract::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\TextInput::make('title')->required()->maxLength(255),
                        Forms\Components\Select::make('vendor_id')->label('Vendor / Contractor')
                            ->options(Vendor::where('company_id', $companyId)->pluck('name', 'id'))->searchable(),
                        Forms\Components\Select::make('type')->options([
                            'main' => 'Main Contract',
                            'sub' => 'Sub-Contract',
                            'supply' => 'Supply',
                            'consultancy' => 'Consultancy',
                            'service' => 'Service',
                            'other' => 'Other',
                        ])->required(),
                        Forms\Components\Select::make('status')->options(Contract::$statuses)->required()->default('draft'),
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('end_date'),
                    ])->columns(2),
                    Section::make('Financial Details')->schema([
                        Forms\Components\TextInput::make('original_value')->numeric()->prefix('$')->required()->default(0),
                        Forms\Components\TextInput::make('revised_value')->numeric()->prefix('$'),
                    ])->columns(2),
                    Section::make('Scope & Terms')->schema([
                        Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('scope')->label('Scope of Work')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('terms')->label('Terms & Conditions')->rows(3)->columnSpanFull(),
                    ])->collapsed(),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    $data['created_by'] = auth()->id();
                    Contract::create($data);
                    Notification::make()->title('Contract created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(Contract::query()->where('cde_project_id', $projectId)->with(['vendor']))
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')->label('Contract #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-document-text'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('vendor.name')->label('Vendor')->placeholder('—'),
                Tables\Columns\TextColumn::make('type')->badge()->color('info'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'active' => 'success', 'draft' => 'gray', 'completed' => 'info', 'terminated' => 'danger', 'suspended' => 'warning', default => 'gray'}),
                Tables\Columns\TextColumn::make('original_value')->formatStateUsing(CurrencyHelper::formatter())->label('Original'),
                Tables\Columns\TextColumn::make('revised_value')->formatStateUsing(CurrencyHelper::formatter())->label('Revised')->placeholder('—'),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date()
                    ->color(fn($record) => $record->end_date?->isPast() && $record->status === 'active' ? 'danger' : null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Contract::$statuses),
                Tables\Filters\SelectFilter::make('type')->options([
                    'main' => 'Main Contract',
                    'sub' => 'Sub-Contract',
                    'supply' => 'Supply',
                    'consultancy' => 'Consultancy',
                    'service' => 'Service',
                ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Contract Info')->schema([
                            Forms\Components\TextInput::make('contract_number')->disabled(),
                            Forms\Components\TextInput::make('title')->disabled(),
                            Forms\Components\TextInput::make('type')->disabled(),
                            Forms\Components\TextInput::make('status')->disabled(),
                            Forms\Components\TextInput::make('start_date')->disabled(),
                            Forms\Components\TextInput::make('end_date')->disabled(),
                            Forms\Components\TextInput::make('original_value')->disabled()->prefix('$'),
                            Forms\Components\TextInput::make('revised_value')->disabled()->prefix('$'),
                        ])->columns(2),
                        Section::make('Scope & Terms')->schema([
                            Forms\Components\Textarea::make('description')->disabled()->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('scope')->disabled()->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('terms')->disabled()->rows(2)->columnSpanFull(),
                        ]),
                    ])
                    ->fillForm(fn(Contract $record) => $record->toArray())
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\Action::make('variation')
                    ->label('Variation')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->schema([
                        Forms\Components\TextInput::make('revised_value')->label('Revised Contract Value')->numeric()->prefix('$')->required(),
                        Forms\Components\Textarea::make('variation_note')->label('Variation Reason')->rows(2),
                    ])
                    ->fillForm(fn(Contract $record) => ['revised_value' => $record->revised_value ?? $record->original_value])
                    ->action(function (array $data, Contract $record): void {
                        $desc = $record->description ? $record->description . "\n" : '';
                        $desc .= '[Variation ' . now()->format('M d Y') . '] ' . ($data['variation_note'] ?? 'Value revised');
                        $record->update(['revised_value' => $data['revised_value'], 'description' => $desc]);
                        Notification::make()->title('Contract variation applied')->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Contract Info')->schema([
                            Forms\Components\TextInput::make('contract_number')->required(),
                            Forms\Components\TextInput::make('title')->required(),
                            Forms\Components\Select::make('vendor_id')
                                ->options(fn() => Vendor::where('company_id', $this->record->company_id)->pluck('name', 'id'))->searchable(),
                            Forms\Components\Select::make('type')->options([
                                'main' => 'Main Contract',
                                'sub' => 'Sub-Contract',
                                'supply' => 'Supply',
                                'consultancy' => 'Consultancy',
                                'service' => 'Service',
                                'other' => 'Other',
                            ])->required(),
                            Forms\Components\Select::make('status')->options(Contract::$statuses)->required(),
                            Forms\Components\DatePicker::make('start_date'),
                            Forms\Components\DatePicker::make('end_date'),
                        ])->columns(2),
                        Section::make('Financial')->schema([
                            Forms\Components\TextInput::make('original_value')->numeric()->prefix('$'),
                            Forms\Components\TextInput::make('revised_value')->numeric()->prefix('$'),
                        ])->columns(2),
                        Section::make('Scope & Terms')->schema([
                            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('scope')->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('terms')->rows(2)->columnSpanFull(),
                        ])->collapsed(),
                    ])
                    ->fillForm(fn(Contract $record) => $record->toArray())
                    ->action(function (array $data, Contract $record): void {
                        $record->update($data);
                        Notification::make()->title('Contract updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Contract $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Contracts')
            ->emptyStateDescription('Create contracts to manage project costs.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }
}
