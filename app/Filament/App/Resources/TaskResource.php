<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string|\UnitEnum|null $navigationGroup = 'Task & Workflow';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Task Details')->schema([
                Infolists\Components\TextEntry::make('title')
                    ->icon('heroicon-o-clipboard-document-check'),
                Infolists\Components\TextEntry::make('project.name')
                    ->label('Project')
                    ->icon('heroicon-o-building-office')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('workOrder.wo_number')
                    ->label('Work Order')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('parent.title')
                    ->label('Parent Task')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning',
                        'medium' => 'info', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'done' => 'success', 'in_progress' => 'info',
                        'review' => 'warning', 'blocked' => 'danger', default => 'gray',
                    }),
            ])->columns(2),

            Schemas\Components\Section::make('Assignment & Progress')->schema([
                Infolists\Components\TextEntry::make('assignee.name')
                    ->label('Assigned To')
                    ->icon('heroicon-o-user')
                    ->placeholder('Unassigned'),
                Infolists\Components\TextEntry::make('due_date')
                    ->date()
                    ->icon('heroicon-o-calendar')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('estimated_hours')
                    ->suffix(' hrs')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('progress_percent')
                    ->suffix('%')
                    ->placeholder('0'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime(),
            ])->columns(3),

            Schemas\Components\Section::make('Description')->schema([
                Infolists\Components\TextEntry::make('description')
                    ->html()
                    ->columnSpanFull()
                    ->placeholder('No description provided.'),
            ])->collapsible(),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Task Details')->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('cde_project_id')
                    ->relationship('project', 'name')->searchable()->preload()->label('Project'),
                Forms\Components\Select::make('work_order_id')
                    ->relationship('workOrder', 'wo_number')->searchable()->preload()->label('Work Order'),
                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'title')->searchable()->preload()->label('Parent Task'),
                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignee', 'name')->searchable()->preload(),
                Forms\Components\Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'])
                    ->default('medium'),
                Forms\Components\Select::make('status')
                    ->options(Task::$statuses)->default('to_do'),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\TextInput::make('estimated_hours')->numeric()->suffix('hrs'),
                Forms\Components\TextInput::make('progress_percent')->numeric()->suffix('%'),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('project.name')->label('Project'),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To'),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning',
                        'medium' => 'info', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'done' => 'success', 'in_progress' => 'info',
                        'review' => 'warning', 'blocked' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('progress_percent')->suffix('%'),
                Tables\Columns\TextColumn::make('due_date')->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Task::$statuses),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignee', 'name')->label('Assignee'),
            ])
            ->actions([Actions\ViewAction::make(), Actions\EditAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
