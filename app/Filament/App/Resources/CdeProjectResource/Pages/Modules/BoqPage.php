<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Boq;
use App\Models\Contract;
use App\Support\CurrencyHelper;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class BoqPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'boq_management';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'BOQ';
    protected static ?string $title = 'BOQ Management';
    protected string $view = 'filament.app.pages.modules.boq';

    public function getStats(): array
    {
        $r = $this->record;
        $total = $r->boqs()->count();
        $approved = $r->boqs()->where('status', 'approved')->count();
        $totalValue = $r->boqs()->sum('total_value');

        return [
            [
                'label' => 'Total BOQs',
                'value' => $total,
                'sub' => $approved . ' approved',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V13.5zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25V18zm2.498-6.75h.007v.008h-.007v-.008zm0 2.25h.007v.008h-.007V13.5zM8.25 6h7.5v2.25h-7.5V6zM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0012 2.25z" /></svg>'
            ],
            [
                'label' => 'Total Value',
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => 'All BOQs combined',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Pending',
                'value' => $r->boqs()->whereNotIn('status', ['approved'])->count(),
                'sub' => 'Awaiting approval',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    protected function getBoqFormSchema(): array
    {
        $projectId = $this->record->id;
        $companyId = $this->record->company_id;
        return [
            Section::make('BOQ Details')->schema([
                Forms\Components\TextInput::make('boq_number')->label('BOQ Number')->required()
                    ->default(fn() => 'BOQ-' . str_pad((string) (Boq::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT)),
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Select::make('contract_id')->label('Contract')
                    ->options(Contract::where('company_id', $companyId)->pluck('title', 'id'))->searchable()->nullable(),
                Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'submitted' => 'Submitted', 'revised' => 'Revised', 'approved' => 'Approved'])->required()->default('draft'),
                Forms\Components\TextInput::make('total_value')->numeric()->prefix('$')->default(0)->label('Total Value'),
                Forms\Components\Select::make('currency')->options(['UGX' => 'UGX', 'USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP'])->default('UGX'),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;
        $companyId = $this->record->company_id;

        return $table
            ->query(Boq::query()->where('cde_project_id', $projectId))
            ->columns([
                Tables\Columns\TextColumn::make('boq_number')->label('BOQ #')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) { 'approved' => 'success', 'draft' => 'gray', 'submitted' => 'info', 'revised' => 'warning', default => 'gray'}),
                Tables\Columns\TextColumn::make('total_value')->formatStateUsing(CurrencyHelper::formatter())->label('Total Value'),
                Tables\Columns\TextColumn::make('contract.title')->label('Contract')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()->label('New BOQ')->icon('heroicon-o-plus')
                    ->schema($this->getBoqFormSchema())
                    ->mutateDataUsing(function (array $data) use ($projectId, $companyId): array {
                        $data['cde_project_id'] = $projectId;
                        $data['company_id'] = $companyId;
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()->schema($this->getBoqFormSchema()),
                EditAction::make()->schema($this->getBoqFormSchema()),
                Action::make('approve')->icon('heroicon-o-check-circle')->color('success')->requiresConfirmation()
                    ->visible(fn(Boq $record) => $record->status !== 'approved')
                    ->action(fn(Boq $record) => $record->update(['status' => 'approved'])),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('No BOQs')
            ->emptyStateDescription('No Bills of Quantities have been created for this project yet.')
            ->emptyStateIcon('heroicon-o-calculator');
    }
}
