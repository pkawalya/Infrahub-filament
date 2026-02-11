<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Models\ProjectFolder;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;

class FoldersRelationManager extends RelationManager
{
    protected static string $relationship = 'folders';

    protected static ?string $title = 'Folders & Documents';

    protected static ?string $modelLabel = 'Folder';

    protected static ?string $pluralModelLabel = 'Folders';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-folder';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Folder name'),

                Select::make('parent_id')
                    ->label('Parent Folder')
                    ->options(function () {
                        return ProjectFolder::getNestedOptions(
                            $this->getOwnerRecord()->id,
                            null,
                            null,
                            ''
                        );
                    })
                    ->placeholder('Root (no parent)')
                    ->searchable()
                    ->preload(),

                ColorPicker::make('color')
                    ->label('Folder Color')
                    ->default('#6B7280'),

                Textarea::make('description')
                    ->rows(2)
                    ->placeholder('Optional description'),

                Hidden::make('created_by')
                    ->default(auth()->id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->width('40px'),

                IconColumn::make('folder_icon')
                    ->label('')
                    ->icon('heroicon-o-folder')
                    ->color(fn(ProjectFolder $record) => $record->parent_id ? 'gray' : 'warning')
                    ->width('40px'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::SemiBold)
                    ->description(fn(ProjectFolder $record) => $record->parent ? 'in ' . $record->parent->name : 'Root folder'),

                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('documents_count')
                    ->label('Documents')
                    ->counts('documents')
                    ->badge()
                    ->color('info'),

                TextColumn::make('children_count')
                    ->label('Subfolders')
                    ->counts('children')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('creator.name')
                    ->label('Created by')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label('Location')
                    ->options(function () {
                        $options = ['' => 'Root folders only'];
                        return $options + ProjectFolder::getNestedOptions($this->getOwnerRecord()->id);
                    })
                    ->placeholder('All folders'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-folder-plus')
                    ->label('New Folder')
                    ->modalWidth('lg')
                    ->closeModalByClickingAway(false),

                Action::make('browseDocuments')
                    ->label('Browse Files')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('info')
                    ->url(fn() => route('filament.admin.pages.project-documents', [
                        'project' => $this->getOwnerRecord()->id,
                    ])),
            ])
            ->recordActions([
                ViewAction::make()
                    ->closeModalByClickingAway(false),
                EditAction::make()
                    ->closeModalByClickingAway(false)
                    ->mutateFormDataUsing(function (array $data, ProjectFolder $record): array {
                        // Prevent moving folder to its own descendant
                        if (isset($data['parent_id']) && $data['parent_id'] === $record->id) {
                            $data['parent_id'] = $record->parent_id;
                        }
                        return $data;
                    }),
                Action::make('openFolder')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->url(fn(ProjectFolder $record) => route('filament.admin.pages.project-documents', [
                        'project' => $this->getOwnerRecord()->id,
                        'folder' => $record->id,
                    ])),
                DeleteAction::make()
                    ->before(function (ProjectFolder $record) {
                        if ($record->hasChildren()) {
                            Notification::make()
                                ->title('Cannot delete folder')
                                ->body('This folder contains documents or subfolders. Please move or delete them first.')
                                ->danger()
                                ->send();

                            return false;
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateHeading('No folders yet')
            ->emptyStateDescription('Create folders to organize your project documents.')
            ->emptyStateIcon('heroicon-o-folder-open');
    }
}
