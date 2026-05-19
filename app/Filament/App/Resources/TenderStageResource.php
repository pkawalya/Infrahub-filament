<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TenderStageResource\Pages;
use App\Models\TenderStage;
use App\Models\TenderStageTransition;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenderStageResource extends Resource
{
    protected static ?string $model = TenderStage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?string $navigationLabel = 'Tender Stages';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationParentItem = 'Tenders & Bids';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Stage Definition')
                ->icon('heroicon-o-tag')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('company_id', auth()->user()?->company_id)),
                    Forms\Components\Select::make('color')
                        ->options([
                            'gray'    => '⬜ Gray',
                            'info'    => '🟦 Blue (Info)',
                            'primary' => '🟪 Purple (Primary)',
                            'warning' => '🟨 Yellow (Warning)',
                            'success' => '🟩 Green (Success)',
                            'danger'  => '🟥 Red (Danger)',
                        ])
                        ->default('gray')
                        ->required(),
                    Forms\Components\TextInput::make('icon')
                        ->placeholder('heroicon-o-flag')
                        ->helperText('Heroicon class name'),
                    Forms\Components\Textarea::make('description')
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower number = earlier in the workflow'),
                    Forms\Components\Toggle::make('is_default')
                        ->label('Default Stage')
                        ->helperText('New tenders will be assigned to this stage automatically'),
                    Forms\Components\Toggle::make('is_terminal')
                        ->label('Terminal Stage')
                        ->helperText('No further transitions allowed from this stage'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])->columns(2),

            Section::make('Allowed Transitions')
                ->icon('heroicon-o-arrows-right-left')
                ->schema([
                    Forms\Components\Repeater::make('outgoingTransitions')
                        ->label('Can transition TO:')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('to_stage_id')
                                ->label('Target Stage')
                                ->options(function () {
                                    $companyId = auth()->user()?->company_id;
                                    return $companyId
                                        ? TenderStage::where('company_id', $companyId)->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')
                                        : [];
                                })
                                ->required()
                                ->searchable(),
                            Forms\Components\TextInput::make('required_permission')
                                ->label('Required Permission')
                                ->placeholder('Optional — e.g. tender.advance_stage'),
                            Forms\Components\Toggle::make('requires_comment')
                                ->label('Require Comment'),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ])
                        ->columns(4)
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $data['company_id'] = auth()->user()->company_id;
                            return $data;
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color(fn(TenderStage $r) => $r->color),
                Tables\Columns\TextColumn::make('slug')
                    ->fontFamily('mono')
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_terminal')
                    ->label('Terminal')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('tenders_count')
                    ->label('Tenders')
                    ->counts('tenders')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('outgoing_transitions_count')
                    ->label('Transitions')
                    ->counts('outgoingTransitions')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (TenderStage $record) {
                        if ($record->tenders()->count() > 0) {
                            throw new \Exception('Cannot delete a stage that has tenders assigned to it.');
                        }
                    }),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTenderStages::route('/'),
            'create' => Pages\CreateTenderStage::route('/create'),
            'edit'   => Pages\EditTenderStage::route('/{record}/edit'),
        ];
    }
}
