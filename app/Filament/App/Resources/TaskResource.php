<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TaskResource\Pages;
use App\Filament\Concerns\UIStandards;
use App\Models\Task;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Task & Workflow';
    protected static ?string $navigationLabel = 'Tasks';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

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
                    ->color(fn(string $state): string => UIStandards::priorityColor($state)),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => UIStandards::statusColor($state)),
            ])->columns(2),

            Schemas\Components\Section::make('Assignment & Progress')->schema([
                Infolists\Components\TextEntry::make('assignee.name')
                    ->label('Assigned To')
                    ->icon('heroicon-o-user')
                    ->placeholder('Unassigned'),
                Infolists\Components\TextEntry::make('due_date')
                    ->date(UIStandards::DATE_FORMAT)
                    ->icon('heroicon-o-calendar')
                    ->placeholder(UIStandards::PLACEHOLDER_DATE),
                Infolists\Components\TextEntry::make('estimated_hours')
                    ->suffix(' hrs')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('progress_percent')
                    ->suffix('%')
                    ->placeholder('0'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(UIStandards::DATETIME_FORMAT),
                Infolists\Components\TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime(UIStandards::DATETIME_FORMAT),
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
                    ->relationship('assignee', 'name', fn($q) => $q->where('company_id', auth()->user()?->company_id)->where('is_active', true))
                    ->searchable()->preload(),
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
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->weight('bold')->limit(UIStandards::LIMIT_TITLE),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->limit(UIStandards::LIMIT_PROJECT),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->limit(UIStandards::LIMIT_NAME),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => UIStandards::priorityColor($state)),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => UIStandards::statusColor($state)),
                Tables\Columns\TextColumn::make('progress_percent')->suffix('%'),
                Tables\Columns\TextColumn::make('due_date')->date(UIStandards::DATE_FORMAT)->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Task::$statuses),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignee', 'name', fn($q) => $q->where('company_id', auth()->user()?->company_id))
                    ->label('Assignee'),
            ])
            ->actions([Actions\ViewAction::make(), Actions\EditAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
