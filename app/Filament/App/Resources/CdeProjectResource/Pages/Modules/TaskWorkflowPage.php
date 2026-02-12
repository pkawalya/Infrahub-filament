<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\Task;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class TaskWorkflowPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'tasks';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Tasks';
    protected static ?string $title = 'Task & Workflow Management';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('task_workflow');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $companyId = $this->getOwnerRecord()->company_id;
        return $schema->components([
            Section::make('Task Details')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('assigned_to')->label('Assign To')
                    ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                Forms\Components\Select::make('priority')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ])->required()->default('medium'),
                Forms\Components\Select::make('status')->options([
                    'todo' => 'To Do',
                    'in_progress' => 'In Progress',
                    'review' => 'Review',
                    'done' => 'Done',
                    'cancelled' => 'Cancelled',
                ])->required()->default('todo'),
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\TextInput::make('progress')->numeric()->suffix('%')->default(0)->minValue(0)->maxValue(100),
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
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'done' => 'success', 'in_progress' => 'info', 'review' => 'primary',
                        'cancelled' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('progress')->suffix('%')->sortable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable()
                    ->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['done', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'todo' => 'To Do',
                    'in_progress' => 'In Progress',
                    'review' => 'Review',
                    'done' => 'Done',
                    'cancelled' => 'Cancelled',
                ]),
                Tables\Filters\SelectFilter::make('priority')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent',
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
            'Tasks',
        ];
    }
}
