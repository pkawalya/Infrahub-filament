<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\Client;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderType;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class CoreFsmPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'workOrders';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Core FSM';
    protected static ?string $title = 'Work Orders';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('core');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $companyId = $this->getOwnerRecord()->company_id;
        return $schema->components([
            Section::make('Work Order Details')->schema([
                Forms\Components\TextInput::make('wo_number')->label('WO Number')
                    ->default(fn() => 'WO-' . str_pad((string) (WorkOrder::where('company_id', $companyId)->count() + 1), 5, '0', STR_PAD_LEFT))
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('work_order_type_id')->label('Type')
                    ->options(WorkOrderType::where('company_id', $companyId)->pluck('name', 'id'))->searchable(),
                Forms\Components\Select::make('client_id')->label('Client')
                    ->options(Client::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                Forms\Components\Select::make('priority')->options(WorkOrder::$priorities)->required()->default('medium'),
                Forms\Components\Select::make('status')->options(WorkOrder::$statuses)->required()->default('pending'),
                Forms\Components\Select::make('assigned_to')->label('Assign To')
                    ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')->label('WO #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'completed' => 'success', 'in_progress' => 'info', 'approved' => 'primary',
                        'on_hold' => 'warning', 'cancelled' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->toggleable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable()
                    ->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['completed', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(WorkOrder::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options(WorkOrder::$priorities),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['company_id'] = $this->getOwnerRecord()->company_id;
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
            'Work Orders',
        ];
    }
}
