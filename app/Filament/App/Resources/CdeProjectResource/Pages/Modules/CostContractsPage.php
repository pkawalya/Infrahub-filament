<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Contract;
use App\Support\CurrencyHelper;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class CostContractsPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'cost_contracts';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $title = 'Cost & Contracts';
    protected string $view = 'filament.app.pages.modules.cost-contracts';

    public function getStats(): array
    {
        $r = $this->record;
        $total = $r->contracts()->count();
        $active = $r->contracts()->where('status', 'active')->count();
        $totalValue = $r->contracts()->sum('original_value');

        return [
            [
                'label' => 'Total Contracts',
                'value' => $total,
                'sub' => $active . ' active',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
            ],
            [
                'label' => 'Total Value',
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => 'All contracts',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Active',
                'value' => $active,
                'sub' => 'In progress',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
        ];
    }

    protected function getContractFormSchema(): array
    {
        $projectId = $this->record->id;
        return [
            Section::make('Contract Information')->schema([
                Forms\Components\TextInput::make('contract_number')->label('Contract #')->required()
                    ->default(fn() => 'CON-' . str_pad((string) (Contract::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT)),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Select::make('type')->options(['main' => 'Main Contract', 'sub' => 'Sub-Contract', 'supply' => 'Supply Contract', 'consultancy' => 'Consultancy', 'service' => 'Service Agreement', 'other' => 'Other'])->required(),
                Forms\Components\Select::make('status')->options(Contract::$statuses)->required()->default('draft'),
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date'),
            ])->columns(2),
            Section::make('Financial Details')->schema([
                Forms\Components\TextInput::make('original_value')->numeric()->prefix('$')->default(0)->label('Original Value'),
                Forms\Components\TextInput::make('revised_value')->numeric()->prefix('$')->label('Revised Value'),
            ])->columns(2),
            Section::make('Details')->schema([
                Forms\Components\Textarea::make('description')->rows(2),
                Forms\Components\Textarea::make('scope')->rows(2)->label('Scope of Work'),
                Forms\Components\Textarea::make('terms')->rows(2)->label('Terms & Conditions')->columnSpanFull(),
            ])->columns(2)->collapsed(),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;
        $companyId = $this->record->company_id;

        return $table
            ->query(Contract::query()->where('cde_project_id', $projectId))
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')->label('Contract #')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) { 'active' => 'success', 'draft' => 'gray', 'completed' => 'info', 'terminated' => 'danger', 'suspended' => 'warning', default => 'gray'}),
                Tables\Columns\TextColumn::make('original_value')->formatStateUsing(CurrencyHelper::formatter())->label('Value'),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Contract::$statuses),
            ])
            ->headerActions([
                Action::make('create')->label('New Contract')->icon('heroicon-o-plus')
                    ->schema($this->getContractFormSchema())
                    ->action(function (array $data) use ($projectId, $companyId): void {
                        $data['cde_project_id'] = $projectId;
                        $data['company_id'] = $companyId;
                        $data['created_by'] = auth()->id();
                        Contract::create($data);
                        Notification::make()->title('Contract created')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('view')->icon('heroicon-o-eye')->color('gray')
                    ->schema($this->getContractFormSchema())
                    ->fillForm(fn(Contract $record) => $record->toArray())
                    ->modalSubmitAction(false),
                Action::make('edit')->icon('heroicon-o-pencil')
                    ->schema($this->getContractFormSchema())
                    ->fillForm(fn(Contract $record) => $record->toArray())
                    ->action(function (array $data, Contract $record): void {
                        $record->update($data);
                        Notification::make()->title('Contract updated')->success()->send();
                    }),
                Action::make('delete')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->action(fn(Contract $record) => $record->delete()),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('No Contracts')
            ->emptyStateDescription('No contracts have been created for this project yet.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
