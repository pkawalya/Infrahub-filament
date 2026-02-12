<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\Milestone;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class PlanningProgressPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'milestones';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Planning';
    protected static ?string $title = 'Planning & Progress';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('planning_progress');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Milestone Details')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('status')->options([
                    'not_started' => 'Not Started',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'delayed' => 'Delayed',
                    'cancelled' => 'Cancelled',
                ])->required()->default('not_started'),
                Forms\Components\DatePicker::make('planned_start')->label('Planned Start'),
                Forms\Components\DatePicker::make('planned_end')->label('Planned End'),
                Forms\Components\DatePicker::make('actual_start')->label('Actual Start'),
                Forms\Components\DatePicker::make('actual_end')->label('Actual End'),
                Forms\Components\TextInput::make('progress')->numeric()->suffix('%')->default(0)->minValue(0)->maxValue(100),
                Forms\Components\TextInput::make('weight')->numeric()->suffix('%')->default(0)->label('Weight (%)'),
            ])->columns(2),
            Section::make('Details')->schema([
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) {
                    'completed' => 'success', 'in_progress' => 'info', 'delayed' => 'danger',
                    'not_started' => 'gray', 'cancelled' => 'danger', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('progress')->suffix('%')->sortable(),
                Tables\Columns\TextColumn::make('planned_start')->date()->label('Plan Start'),
                Tables\Columns\TextColumn::make('planned_end')->date()->label('Plan End'),
                Tables\Columns\TextColumn::make('actual_start')->date()->label('Actual Start')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('actual_end')->date()->label('Actual End')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('weight')->suffix('%'),
            ])
            ->defaultSort('planned_start', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'not_started' => 'Not Started',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'delayed' => 'Delayed',
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
            'Planning',
        ];
    }
}
