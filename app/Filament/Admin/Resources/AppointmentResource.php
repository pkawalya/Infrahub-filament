<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone-call';
    protected static string|\UnitEnum|null $navigationGroup = 'Platform Management';
    protected static ?string $navigationLabel = 'Demo Bookings';
    protected static ?string $modelLabel = 'Demo Booking';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Contact Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('company')
                    ->label('Company Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('company_size')
                    ->label('Team Size')
                    ->placeholder('e.g. 6-20 people')
                    ->maxLength(50),
                Forms\Components\TextInput::make('job_title')
                    ->label('Job Title / Role')
                    ->maxLength(255),
            ])->columns(2),

            Schemas\Components\Section::make('Schedule & Status')->schema([
                Forms\Components\DatePicker::make('preferred_date')
                    ->label('Preferred Date')
                    ->required(),
                Forms\Components\TextInput::make('preferred_time')
                    ->label('Preferred Time')
                    ->required(),
                Forms\Components\TextInput::make('timezone')
                    ->label('Timezone')
                    ->default('EAT'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('message')
                    ->label('Message / Notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Booking Overview')->schema([
                Infolists\Components\TextEntry::make('name')
                    ->label('Full Name')
                    ->weight('bold'),
                Infolists\Components\TextEntry::make('email')
                    ->label('Email')
                    ->icon('heroicon-o-envelope')
                    ->copyable(),
                Infolists\Components\TextEntry::make('phone')
                    ->label('Phone')
                    ->icon('heroicon-o-phone')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('company')
                    ->label('Company')
                    ->icon('heroicon-o-building-office')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('company_size')
                    ->label('Team Size')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('job_title')
                    ->label('Job Title')
                    ->placeholder('—'),
            ])->columns(2),

            Schemas\Components\Section::make('Schedule Information')->schema([
                Infolists\Components\TextEntry::make('preferred_date')
                    ->label('Preferred Date')
                    ->date('l, F j, Y'),
                Infolists\Components\TextEntry::make('preferred_time')
                    ->label('Time Slot'),
                Infolists\Components\TextEntry::make('timezone')
                    ->label('Timezone'),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    }),
                Infolists\Components\TextEntry::make('message')
                    ->label('Submitted Notes / Goals')
                    ->placeholder('No notes provided')
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Submitted At')
                    ->dateTime(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Contact')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                Tables\Columns\TextColumn::make('company')
                    ->label('Company')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('company_size')
                    ->label('Team Size')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('preferred_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('preferred_time')
                    ->label('Time Slot'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn(Appointment $record) => $record->status === 'pending')
                    ->action(fn(Appointment $record) => $record->update(['status' => 'confirmed'])),
                Actions\Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn(Appointment $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->action(fn(Appointment $record) => $record->update(['status' => 'completed'])),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
