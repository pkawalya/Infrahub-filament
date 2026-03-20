<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ClientProjectResource\Pages;
use App\Models\CdeProject;
use Filament\Actions;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientProjectResource extends Resource
{
    protected static ?string $model = CdeProject::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Projects';
    protected static ?string $modelLabel = 'Project';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $settings = auth()->user()?->company?->settings['client_portal'] ?? [];
        return $settings['show_projects'] ?? true;
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        return parent::getEloquentQuery()
            ->where(function (Builder $query) use ($user) {
                // Projects where the user is the assigned client
                $query->whereHas('client', fn(Builder $q) => $q->where('user_id', $user?->id))
                    // OR projects where the user is an invited member
                    ->orWhereHas('members', fn(Builder $q) => $q->where('users.id', $user?->id));
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('project_code')->badge()->color('primary'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $s) => match ($s) {
                    'active' => 'success', 'completed' => 'info', 'on_hold' => 'warning', default => 'gray'
                }),
                Tables\Columns\TextColumn::make('start_date')->date('M d, Y'),
                Tables\Columns\TextColumn::make('end_date')->date('M d, Y'),
            ])
            ->defaultSort('name')
            ->actions([Actions\ViewAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        $settings = auth()->user()?->company?->settings['client_portal'] ?? [];
        $showTimeline = $settings['show_project_timeline'] ?? true;
        $showBudget = $settings['show_project_budget'] ?? false;
        $showTeam = $settings['show_project_team'] ?? false;

        $fields = [
            Infolists\Components\TextEntry::make('name'),
            Infolists\Components\TextEntry::make('code')->label('Project Code')->badge(),
            Infolists\Components\TextEntry::make('status')->badge()->color(fn(string $state) => match ($state) {
                'active' => 'success', 'completed' => 'info', 'on_hold' => 'warning', default => 'gray'
            }),
            Infolists\Components\TextEntry::make('description')->columnSpanFull(),
        ];

        if ($showTimeline) {
            $fields[] = Infolists\Components\TextEntry::make('start_date')->date();
            $fields[] = Infolists\Components\TextEntry::make('end_date')->date();
        }

        if ($showBudget) {
            $fields[] = Infolists\Components\TextEntry::make('budget')
                ->formatStateUsing(fn($record) => $record->formatCurrency($record->budget));
        }

        if ($showTeam) {
            $fields[] = Infolists\Components\TextEntry::make('manager.name')->label('Project Manager');
            $fields[] = Infolists\Components\TextEntry::make('members_count')
                ->label('Team Size')
                ->getStateUsing(fn($record) => $record->members()->count() . ' members');
        }

        return $schema->schema([
            Section::make('Project Details')->schema($fields)->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientProjects::route('/'),
            'view' => Pages\ViewClientProject::route('/{record}'),
        ];
    }
}
