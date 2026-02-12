<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\Contract;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class CostContractsPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'contracts';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $title = 'Cost & Contracts';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('cost_contracts');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $projectId = $this->getOwnerRecord()->id;
        return $schema->components([
            Section::make('Contract Information')->schema([
                Forms\Components\TextInput::make('contract_number')->label('Contract #')
                    ->default(fn() => 'CON-' . str_pad((string) (Contract::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT))
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Select::make('type')->options([
                    'main' => 'Main Contract',
                    'sub' => 'Sub-Contract',
                    'supply' => 'Supply Contract',
                    'consultancy' => 'Consultancy',
                    'service' => 'Service Agreement',
                    'other' => 'Other',
                ])->required(),
                Forms\Components\Select::make('status')->options(Contract::$statuses)->required()->default('draft'),
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date'),
            ])->columns(2),
            Section::make('Financial Details')->schema([
                Forms\Components\TextInput::make('original_value')->numeric()->prefix('$')->default(0)->label('Original Value'),
                Forms\Components\TextInput::make('revised_value')->numeric()->prefix('$')->label('Revised Value'),
            ])->columns(2),
            Section::make('Details')->schema([
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('scope')->rows(3)->label('Scope of Work')->columnSpanFull(),
                Forms\Components\Textarea::make('terms')->rows(3)->label('Terms & Conditions')->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')->label('Contract #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) {
                    'active' => 'success', 'draft' => 'gray', 'completed' => 'info',
                    'terminated' => 'danger', 'suspended' => 'warning', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('original_value')->formatStateUsing(CurrencyHelper::formatter())->label('Value'),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('end_date')->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Contract::$statuses),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['company_id'] = $this->getOwnerRecord()->company_id;
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title;
    }

    public function getBreadcrumbs(): array
    {
        return [
            CdeProjectResource::getUrl() => 'Projects',
            CdeProjectResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
            'Contracts',
        ];
    }
}
