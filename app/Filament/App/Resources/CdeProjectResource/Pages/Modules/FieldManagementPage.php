<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\DailySiteLog;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class FieldManagementPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'dailySiteLogs';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Field Mgmt';
    protected static ?string $title = 'Field Management';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('field_management');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Daily Site Log')->schema([
                Forms\Components\DatePicker::make('log_date')->required()->default(now()),
                Forms\Components\Select::make('weather')->options([
                    'sunny' => 'Sunny',
                    'cloudy' => 'Cloudy',
                    'rainy' => 'Rainy',
                    'stormy' => 'Stormy',
                    'windy' => 'Windy',
                    'snow' => 'Snow',
                ])->default('sunny'),
                Forms\Components\TextInput::make('temperature')->numeric()->suffix('°C'),
                Forms\Components\TextInput::make('manpower_count')->label('Workers on Site')->numeric()->default(0),
            ])->columns(2),
            Section::make('Activities')->schema([
                Forms\Components\Textarea::make('activities_performed')->label('Activities Performed')->rows(4)->columnSpanFull(),
                Forms\Components\Textarea::make('delays_encountered')->label('Delays/Issues')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('materials_used')->label('Materials Used')->rows(2)->columnSpanFull(),
                Forms\Components\Textarea::make('remarks')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_date')->date()->sortable()->label('Date'),
                Tables\Columns\TextColumn::make('weather')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'sunny' => 'success', 'cloudy' => 'gray', 'rainy' => 'info',
                        'stormy' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('temperature')->suffix('°C'),
                Tables\Columns\TextColumn::make('manpower_count')->label('Workers'),
                Tables\Columns\TextColumn::make('activities_performed')->limit(60)->label('Activities'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('log_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('weather')->options([
                    'sunny' => 'Sunny',
                    'cloudy' => 'Cloudy',
                    'rainy' => 'Rainy',
                    'stormy' => 'Stormy',
                    'windy' => 'Windy',
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
            'Field Management',
        ];
    }
}
