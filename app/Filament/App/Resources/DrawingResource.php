<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DrawingResource\Pages;
use App\Models\Drawing;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DrawingResource extends Resource
{
    protected static ?string $model = Drawing::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static string|\UnitEnum|null $navigationGroup = 'Projects';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Drawings';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        $cid = auth()->user()?->company_id;
        return $schema->schema([
            Section::make('Drawing Information')->schema([
                Forms\Components\TextInput::make('drawing_number')->required()->maxLength(50)
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('company_id', $cid)),
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\Select::make('cde_project_id')->label('Project')
                    ->relationship('project', 'name', fn($q) => $q->where('company_id', $cid))
                    ->searchable()->preload()->required(),
                Forms\Components\Select::make('discipline')->options(Drawing::$disciplines)->default('architectural')->required(),
                Forms\Components\Select::make('drawing_type')->options(Drawing::$drawingTypes)->default('plan')->required(),
                Forms\Components\TextInput::make('current_revision')->default('A')->maxLength(10),
                Forms\Components\Select::make('status')->options(Drawing::$statuses)->default('wip')->required(),
                Forms\Components\TextInput::make('scale')->placeholder('1:100')->maxLength(20),
                Forms\Components\Select::make('sheet_size')->options([
                    'A0' => 'A0',
                    'A1' => 'A1',
                    'A2' => 'A2',
                    'A3' => 'A3',
                    'A4' => 'A4',
                ]),
            ])->columns(3),

            Section::make('ISO 19650 Metadata')->schema([
                Forms\Components\Select::make('suitability_code')->options(Drawing::$suitabilityCodes),
                Forms\Components\TextInput::make('originator')->maxLength(50),
                Forms\Components\TextInput::make('zone')->maxLength(50),
                Forms\Components\TextInput::make('level')->maxLength(50),
            ])->columns(4)->collapsed(),

            Section::make('Responsibility')->schema([
                Forms\Components\Select::make('drawn_by')->label('Drawn By')
                    ->relationship('drawnByUser', 'name', fn($q) => $q->where('company_id', $cid))->searchable()->preload(),
                Forms\Components\DatePicker::make('drawn_date'),
                Forms\Components\Select::make('checked_by')->label('Checked By')
                    ->relationship('checkedByUser', 'name', fn($q) => $q->where('company_id', $cid))->searchable()->preload(),
                Forms\Components\DatePicker::make('checked_date'),
                Forms\Components\Select::make('approved_by')->label('Approved By')
                    ->relationship('approvedByUser', 'name', fn($q) => $q->where('company_id', $cid))->searchable()->preload(),
                Forms\Components\DatePicker::make('approved_date'),
            ])->columns(3)->collapsed(),

            Section::make('Additional')->schema([
                Forms\Components\TagsInput::make('tags'),
                Forms\Components\Textarea::make('notes')->rows(3),
                Forms\Components\Textarea::make('description')->rows(3),
            ])->collapsed(),

            Forms\Components\Hidden::make('company_id')->default(fn() => $cid),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('drawing_number')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->limit(20),
                Tables\Columns\TextColumn::make('discipline')->badge()->color(fn(string $s) => match ($s) {
                    'architectural' => 'primary', 'structural' => 'danger', 'mechanical' => 'warning',
                    'electrical' => 'info', 'civil' => 'success', default => 'gray'
                }),
                Tables\Columns\TextColumn::make('current_revision')->badge()->color('warning')->label('Rev'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $s) => match ($s) {
                    'wip' => 'gray', 'for_review' => 'warning', 'approved' => 'success',
                    'ifc' => 'primary', 'as_built' => 'info', 'superseded' => 'danger', default => 'gray'
                })->formatStateUsing(fn(string $s) => Drawing::$statuses[$s] ?? $s),
                Tables\Columns\TextColumn::make('suitability_code')->badge()->color('info')->placeholder('—'),
                Tables\Columns\TextColumn::make('sheet_size')->placeholder('—'),
                Tables\Columns\TextColumn::make('drawnByUser.name')->label('Drawn By')->limit(15)->placeholder('—'),
                Tables\Columns\TextColumn::make('revisions_count')->counts('revisions')->badge()->color('gray')->label('Revisions'),
            ])
            ->defaultSort('drawing_number')
            ->filters([
                Tables\Filters\SelectFilter::make('discipline')->options(Drawing::$disciplines),
                Tables\Filters\SelectFilter::make('status')->options(Drawing::$statuses),
                Tables\Filters\SelectFilter::make('cde_project_id')->label('Project')->relationship('project', 'name'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('newRevision')
                    ->icon('heroicon-o-arrow-path')->color('warning')->label('New Revision')
                    ->form([
                        Forms\Components\TextInput::make('revision_code')->required()->maxLength(10),
                        Forms\Components\Textarea::make('revision_description')->rows(2),
                        Forms\Components\FileUpload::make('file')->directory('drawings'),
                    ])
                    ->action(function (Drawing $record, array $data): void {
                        // Supersede old current revision
                        $record->revisions()->where('status', 'current')->update(['status' => 'superseded']);

                        // Create new revision
                        $record->revisions()->create([
                            'revision_code' => $data['revision_code'],
                            'revision_description' => $data['revision_description'] ?? null,
                            'file_path' => $data['file'] ?? null,
                            'status' => 'current',
                            'revision_date' => now(),
                            'revised_by' => auth()->id(),
                        ]);

                        $record->update(['current_revision' => $data['revision_code']]);
                    }),
            ])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrawings::route('/'),
            'create' => Pages\CreateDrawing::route('/create'),
            'view' => Pages\ViewDrawing::route('/{record}'),
            'edit' => Pages\EditDrawing::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', 'for_review')->count();
        return $count > 0 ? (string) $count : null;
    }
}
