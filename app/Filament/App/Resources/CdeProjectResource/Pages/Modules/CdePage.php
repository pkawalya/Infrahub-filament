<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\CdeDocument;
use App\Models\CdeFolder;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class CdePage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'documents';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Documents';
    protected static ?string $title = 'CDE Documents';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('cde');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $projectId = $this->getOwnerRecord()->id;
        return $schema->components([
            Section::make('Document Information')->schema([
                Forms\Components\TextInput::make('document_number')->label('Doc #')
                    ->default(fn() => 'DOC-' . str_pad((string) (CdeDocument::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT))
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Select::make('cde_folder_id')->label('Folder')
                    ->options(CdeFolder::where('cde_project_id', $projectId)->pluck('name', 'id'))->searchable()->nullable(),
                Forms\Components\Select::make('status')->options([
                    'wip' => 'Work in Progress',
                    'shared' => 'Shared',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ])->required()->default('wip'),
                Forms\Components\Select::make('revision')->options([
                    'A' => 'Rev A',
                    'B' => 'Rev B',
                    'C' => 'Rev C',
                    'D' => 'Rev D',
                ])->default('A'),
                Forms\Components\Select::make('suitability')->options([
                    'S0' => 'S0 - WIP',
                    'S1' => 'S1 - Coordination',
                    'S2' => 'S2 - Information',
                    'S3' => 'S3 - Review & Comment',
                    'S4' => 'S4 - Stage Approval',
                    'S6' => 'S6 - PIM Handover',
                    'S7' => 'S7 - AIM Handover',
                ])->default('S0'),
            ])->columns(2),
            Section::make('Details')->schema([
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('document_number')->label('Doc #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('folder.name')->label('Folder')->placeholder('Root'),
                Tables\Columns\TextColumn::make('revision')->badge()->color('info'),
                Tables\Columns\TextColumn::make('suitability')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) {
                    'published' => 'success', 'shared' => 'info', 'wip' => 'warning', 'archived' => 'gray', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'wip' => 'Work in Progress',
                    'shared' => 'Shared',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ]),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['uploaded_by'] = auth()->id();
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
            'Documents',
        ];
    }
}
