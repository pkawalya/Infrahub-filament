<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CrewAttendanceResource\Pages;
use App\Filament\Concerns\UIStandards;
use App\Models\Company;
use App\Models\CrewAttendance;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CrewAttendanceResource extends Resource
{
    protected static ?string $model = CrewAttendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?string $navigationLabel = 'Crew Attendance';
    protected static ?int $navigationSort = 7;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Attendance Record')
                ->icon('heroicon-o-clock')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Worker')
                        ->relationship('worker', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('cde_project_id')
                        ->label('Project / Site')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Forms\Components\DatePicker::make('attendance_date')
                        ->required()
                        ->default(now()),
                    Forms\Components\Select::make('status')
                        ->options(fn() => Company::options('attendance_statuses'))
                        ->default('present')
                        ->required(),
                ])->columns(2),

            Section::make('Time Tracking')
                ->icon('heroicon-o-calculator')
                ->schema([
                    Forms\Components\TimePicker::make('clock_in')
                        ->label('Clock In')
                        ->seconds(false),
                    Forms\Components\TimePicker::make('clock_out')
                        ->label('Clock Out')
                        ->seconds(false),
                    Forms\Components\TextInput::make('hours_worked')
                        ->label('Hours Worked')
                        ->numeric()
                        ->suffix('hrs'),
                    Forms\Components\TextInput::make('overtime_hours')
                        ->label('Overtime')
                        ->numeric()
                        ->suffix('hrs')
                        ->default(0),
                ])->columns(4),

            Section::make('Details')
                ->schema([
                    Forms\Components\TextInput::make('site_location')
                        ->label('Site Location'),
                    Forms\Components\Textarea::make('notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('worker.name')
                    ->label('Worker')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->placeholder('— Office —')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('clock_in')
                    ->label('In')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('clock_out')
                    ->label('Out')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('hours_worked')
                    ->label('Hours')
                    ->suffix('h')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('overtime_hours')
                    ->label('OT')
                    ->suffix('h')
                    ->placeholder('—')
                    ->color('warning'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => UIStandards::statusColor($state)),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(fn() => Company::options('attendance_statuses')),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Worker')
                    ->relationship('worker', 'name'),
                Tables\Filters\SelectFilter::make('cde_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->actions([
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
            'index' => Pages\ListCrewAttendances::route('/'),
            'create' => Pages\CreateCrewAttendance::route('/create'),
            'edit' => Pages\EditCrewAttendance::route('/{record}/edit'),
        ];
    }
}
