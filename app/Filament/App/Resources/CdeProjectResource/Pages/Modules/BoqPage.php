<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\Boq;
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

class BoqPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'boqs';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'BOQ';
    protected static ?string $title = 'Bill of Quantities';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('boq_management');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $projectId = $this->getOwnerRecord()->id;
        return $schema->components([
            Section::make('BOQ Item Details')->schema([
                Forms\Components\TextInput::make('item_code')->label('Item Code')
                    ->default(fn() => 'BOQ-' . str_pad((string) (Boq::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT))
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('description')->required()->maxLength(255),
                Forms\Components\Select::make('contract_id')->label('Contract')
                    ->options(Contract::where('cde_project_id', $projectId)->pluck('title', 'id'))->searchable()->nullable(),
                Forms\Components\Select::make('category')->options([
                    'preliminaries' => 'Preliminaries',
                    'substructure' => 'Substructure',
                    'superstructure' => 'Superstructure',
                    'finishes' => 'Finishes',
                    'services' => 'Services',
                    'external_works' => 'External Works',
                    'other' => 'Other',
                ])->required()->default('other'),
                Forms\Components\TextInput::make('unit')->required()->placeholder('e.g. m², m³, kg, nr'),
                Forms\Components\TextInput::make('quantity')->numeric()->required()->default(0),
                Forms\Components\TextInput::make('rate')->numeric()->prefix('$')->required()->default(0),
                Forms\Components\TextInput::make('amount')->numeric()->prefix('$')->default(0),
            ])->columns(2),
            Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_code')->label('Code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('description')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('quantity')->numeric(2),
                Tables\Columns\TextColumn::make('rate')->formatStateUsing(CurrencyHelper::formatter()),
                Tables\Columns\TextColumn::make('amount')->formatStateUsing(CurrencyHelper::formatter()),
                Tables\Columns\TextColumn::make('contract.title')->label('Contract')->limit(30)->toggleable(),
            ])
            ->defaultSort('item_code', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options([
                    'preliminaries' => 'Preliminaries',
                    'substructure' => 'Substructure',
                    'superstructure' => 'Superstructure',
                    'finishes' => 'Finishes',
                    'services' => 'Services',
                    'external_works' => 'External Works',
                    'other' => 'Other',
                ]),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
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
            'BOQ',
        ];
    }
}
