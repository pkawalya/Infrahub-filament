<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DailySiteDiaryResource\Pages;
use App\Models\Company;
use App\Models\DailySiteDiary;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailySiteDiaryResource extends Resource
{
    protected static ?string $model = DailySiteDiary::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Projects';
    protected static ?string $navigationLabel = 'Daily Site Diary';
    protected static ?string $modelLabel = 'Site Diary';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->whereNull('approved_by')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Date & Project')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\Select::make('cde_project_id')
                        ->label('Project')
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\DatePicker::make('diary_date')
                        ->label('Date')
                        ->required()
                        ->default(now()),
                    Forms\Components\Select::make('weather')
                        ->options(fn() => Company::options('weather_types'))
                        ->searchable(),
                    Forms\Components\TextInput::make('temperature')
                        ->numeric()
                        ->suffix('°C'),
                ])->columns(4),

            Section::make('Workforce')
                ->icon('heroicon-o-users')
                ->schema([
                    Forms\Components\TextInput::make('workers_on_site')
                        ->label('Own Workers')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\TextInput::make('subcontractor_workers')
                        ->label('Subcontractor Workers')
                        ->numeric()
                        ->default(0),
                ])->columns(2),

            Section::make('Equipment on Site')
                ->icon('heroicon-o-truck')
                ->schema([
                    Forms\Components\TextInput::make('equipment_on_site')
                        ->label('Equipment Count')
                        ->numeric()
                        ->default(0),
                ])->columns(2),

            Section::make('Work Summary')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    Forms\Components\Textarea::make('work_performed')
                        ->label('Work Performed Today')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Describe the work activities completed today...'),
                    Forms\Components\Textarea::make('work_planned_tomorrow')
                        ->label('Work Planned for Tomorrow')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Activities planned for the next working day...'),
                ]),

            Section::make('Issues, Safety & Quality')
                ->icon('heroicon-o-exclamation-triangle')
                ->schema([
                    Forms\Components\Textarea::make('delays')
                        ->label('Delays / Issues')
                        ->rows(2)
                        ->placeholder('Any delays, stoppages, or issues encountered'),
                    Forms\Components\Textarea::make('safety_observations')
                        ->label('Safety Observations')
                        ->rows(2)
                        ->placeholder('Near-misses, hazards observed, PPE compliance'),
                    Forms\Components\Textarea::make('quality_observations')
                        ->label('Quality Observations')
                        ->rows(2)
                        ->placeholder('Quality checks, NCRs, rework needed'),
                    Forms\Components\Textarea::make('visitor_log')
                        ->label('Visitors')
                        ->rows(2)
                        ->placeholder('Name (Company) - Time in/out'),
                    Forms\Components\Textarea::make('deliveries')
                        ->label('Deliveries Received')
                        ->rows(2)
                        ->placeholder('Materials delivered today'),
                ])->columns(2)->collapsed(),

            Section::make('Site Photos')
                ->icon('heroicon-o-camera')
                ->schema([
                    Forms\Components\FileUpload::make('photos')
                        ->label('Photos')
                        ->image()
                        ->multiple()
                        ->directory('site-diary-photos')
                        ->maxSize(10240)
                        ->maxFiles(10)
                        ->columnSpanFull(),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('diary_date')
                    ->label('Date')
                    ->date('D, M d Y')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weather')
                    ->label('Weather')
                    ->formatStateUsing(fn(?string $state) => Company::options('weather_types')[$state] ?? $state ?? '—'),
                Tables\Columns\TextColumn::make('total_workforce')
                    ->label('Workers')
                    ->state(fn(DailySiteDiary $r) => $r->total_workforce)
                    ->description(fn(DailySiteDiary $r) => $r->workers_on_site . ' own + ' . $r->subcontractor_workers . ' sub')
                    ->color('info'),
                Tables\Columns\TextColumn::make('equipment_on_site')
                    ->label('Equipment')
                    ->placeholder('0'),
                Tables\Columns\TextColumn::make('work_performed')
                    ->label('Summary')
                    ->limit(50)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('approved')
                    ->label('Approved')
                    ->state(fn(DailySiteDiary $r) => $r->isApproved())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('preparer.name')
                    ->label('By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('diary_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cde_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
                Tables\Filters\Filter::make('unapproved')
                    ->label('Pending Approval')
                    ->query(fn($q) => $q->whereNull('approved_by'))
                    ->toggle(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(DailySiteDiary $r) => !$r->isApproved())
                    ->action(fn(DailySiteDiary $r) => $r->update([
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailySiteDiaries::route('/'),
            'create' => Pages\CreateDailySiteDiary::route('/create'),
            'edit' => Pages\EditDailySiteDiary::route('/{record}/edit'),
        ];
    }
}
