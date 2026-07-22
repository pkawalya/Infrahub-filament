<?php

namespace App\Filament\App\Pages;

use App\Models\CdeActivityLog;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use UnitEnum;

class ActivityLogPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Activity Log';

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 98;

    protected static ?string $title = 'Activity Log';

    protected string $view = 'filament.pages.activity-log';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CdeActivityLog::query()
                    ->where('company_id', auth()->user()->company_id)
                    ->with('user')
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'created', 'approved', 'submitted' => 'success',
                        'updated', 'status_changed' => 'warning',
                        'deleted', 'rejected' => 'danger',
                        'viewed', 'downloaded', 'uploaded', 'shared', 'commented' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => CdeActivityLog::$actions[$state] ?? Str::title(str_replace('_', ' ', $state)))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(80)
                    ->searchable(),

                TextColumn::make('loggable_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Action')
                    ->options(CdeActivityLog::$actions)
                    ->multiple(),

                SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn () => User::where('company_id', auth()->user()->company_id)->pluck('name', 'id'))
                    ->searchable(),

                Filter::make('created_at')
                    ->label('Date Range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('From'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Until'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                        ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
                    ),
            ])
            ->paginated([25, 50, 100]);
    }
}
