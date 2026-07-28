<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\ExportsTableCsv;
use App\Filament\App\Resources\ProjectSuggestionResource\Pages;
use App\Models\CdeProject;
use App\Models\ProjectSuggestion;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectSuggestionResource extends Resource
{
    use ExportsTableCsv;

    protected static ?string $model = ProjectSuggestion::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Suggestion Box';
    protected static ?string $modelLabel = 'Suggestion';
    protected static ?string $pluralModelLabel = 'Suggestions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->where('company_id', auth()->user()?->company_id)
            ->with(['project', 'author', 'responder']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Suggestion Details')->schema([
                Forms\Components\Select::make('cde_project_id')
                    ->label('Project Context')
                    ->relationship('project', 'name', fn($q) => $q->where('company_id', auth()->user()?->company_id))
                    ->placeholder('🏢 General Company Suggestion')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\Select::make('category')
                    ->options(ProjectSuggestion::$categories)
                    ->default('general')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('priority')
                    ->options(ProjectSuggestion::$priorities)
                    ->default('normal')
                    ->required()
                    ->native(false),

                Forms\Components\Select::make('status')
                    ->options(ProjectSuggestion::$statuses)
                    ->default('new')
                    ->required()
                    ->native(false),
            ])->columns(4),

            Section::make('Feedback Content')->schema([
                Forms\Components\Textarea::make('content')
                    ->label('Suggestion Text')
                    ->rows(4)
                    ->required()
                    ->columnSpanFull(),
            ]),

            Section::make('Response & Resolution')->schema([
                Forms\Components\Textarea::make('admin_response')
                    ->label('Official Response')
                    ->placeholder('Write an admin response to this suggestion...')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Select::make('responded_by')
                    ->relationship('responder', 'name', fn($q) => $q->where('company_id', auth()->user()?->company_id))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\DateTimePicker::make('responded_at'),
            ])->columns(2),

            Forms\Components\Hidden::make('company_id')
                ->default(fn() => auth()->user()?->company_id),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('priority')
                    ->label('')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'urgent' => '🔴',
                        'high' => '🟠',
                        'normal' => '🟢',
                        'low' => '⚪',
                        default => '🟢',
                    })
                    ->tooltip(fn($record) => ucfirst($record->priority ?? 'normal') . ' priority')
                    ->alignCenter()
                    ->grow(false),

                Tables\Columns\TextColumn::make('project_display')
                    ->label('Scope')
                    ->badge()
                    ->color(fn($record) => $record->cde_project_id ? 'info' : 'warning')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('project', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }),

                Tables\Columns\TextColumn::make('author_display')
                    ->label('Submitted By')
                    ->getStateUsing(fn($record) => $record->author_display)
                    ->icon(fn($record) => $record->is_anonymous ? 'heroicon-o-eye-slash' : 'heroicon-o-user')
                    ->color(fn($record) => $record->is_anonymous ? 'gray' : 'primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'safety' => 'danger',
                        'process' => 'info',
                        'equipment' => 'warning',
                        'communication' => 'primary',
                        'work_conditions' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ProjectSuggestion::$categories[$state] ?? $state),

                Tables\Columns\TextColumn::make('content')
                    ->label('Suggestion')
                    ->limit(75)
                    ->wrap()
                    ->tooltip(fn($record) => $record->content),

                Tables\Columns\TextColumn::make('upvotes')
                    ->label('👍')
                    ->badge()
                    ->color(fn(int $state) => $state > 5 ? 'success' : ($state > 0 ? 'primary' : 'gray'))
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'new' => 'info',
                        'reviewed' => 'warning',
                        'in_progress' => 'primary',
                        'implemented' => 'success',
                        'dismissed' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ProjectSuggestion::$statuses[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cde_project_id')
                    ->label('Project')
                    ->options(fn() => CdeProject::where('company_id', auth()->user()?->company_id)->pluck('name', 'id'))
                    ->placeholder('All Suggestions (Company & Projects)'),

                Tables\Filters\SelectFilter::make('status')
                    ->options(ProjectSuggestion::$statuses),

                Tables\Filters\SelectFilter::make('category')
                    ->options(ProjectSuggestion::$categories),

                Tables\Filters\SelectFilter::make('priority')
                    ->options(ProjectSuggestion::$priorities),
            ])
            ->actions([
                Actions\Action::make('upvote')
                    ->label(fn($record) => '👍 ' . $record->upvotes)
                    ->color('gray')
                    ->size('sm')
                    ->action(function ($record) {
                        $record->increment('upvotes');
                        Notification::make()->success()->title('Upvoted!')->send();
                    }),

                Actions\Action::make('respond')
                    ->label('Respond')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Update Status')
                            ->options(ProjectSuggestion::$statuses)
                            ->required()
                            ->default(fn($record) => $record->status)
                            ->native(false),

                        Forms\Components\Textarea::make('admin_response')
                            ->label('Official Response')
                            ->placeholder('Type response to this suggestion...')
                            ->rows(3)
                            ->required()
                            ->default(fn($record) => $record->admin_response),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                            'admin_response' => $data['admin_response'],
                            'responded_by' => auth()->id(),
                            'responded_at' => now(),
                        ]);

                        Notification::make()->success()->title('Response recorded')->send();
                    }),

                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectSuggestions::route('/'),
            'create' => Pages\CreateProjectSuggestion::route('/create'),
            'edit' => Pages\EditProjectSuggestion::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $newCount = (int) static::getEloquentQuery()->where('status', 'new')->count();
        return $newCount > 0 ? (string) $newCount : null;
    }
}
