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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
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
        return $schema->schema([
            Section::make('Project Details')->schema([
                Infolists\Components\TextEntry::make('name'),
                Infolists\Components\TextEntry::make('project_code')->badge(),
                Infolists\Components\TextEntry::make('status')->badge(),
                Infolists\Components\TextEntry::make('start_date')->date(),
                Infolists\Components\TextEntry::make('end_date')->date(),
                Infolists\Components\TextEntry::make('description'),
            ])->columns(2),
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
