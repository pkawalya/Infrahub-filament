<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WorkflowTemplateResource\Pages;
use App\Models\User;
use App\Models\WorkflowTemplate;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkflowTemplateResource extends Resource
{
    protected static ?string $model = WorkflowTemplate::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Workflow Templates';
    protected static ?string $modelLabel = 'Workflow Template';

    public static array $moduleTypes = [
        'MaterialRequisition' => 'Material Requisitions',
        'PaymentCertificate' => 'Payment Certificates',
        'Invoice' => 'Invoices & Billing',
        'ChangeOrder' => 'Change Orders',
        'Rfi' => 'RFIs (Request for Information)',
        'SafetyIncident' => 'Safety Incident Reports',
        'WorkOrder' => 'Work Orders',
        'Tender' => 'Tenders & Bidding',
    ];

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Template Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Template Name')
                    ->placeholder('e.g. Standard 2-Step Payment Approval')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('module_type')
                    ->label('Module Type')
                    ->options(self::$moduleTypes)
                    ->required()
                    ->native(false)
                    ->helperText('Select which company module this approval workflow applies to.'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active Template')
                    ->default(true)
                    ->helperText('Only one active template is permitted per module type.'),
            ])->columns(3),

            Section::make('Workflow Approval Steps')
                ->description('Define ordered approval stages. Submissions will progress through these steps sequentially.')
                ->schema([
                    Forms\Components\Repeater::make('steps')
                        ->relationship('steps')
                        ->schema([
                            Forms\Components\TextInput::make('step_sequence')
                                ->label('Seq #')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('name')
                                ->label('Step Name')
                                ->placeholder('e.g. Technical Manager Review')
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\Select::make('approver_role')
                                ->label('Required Role')
                                ->options([
                                    'company_admin' => 'Company Admin',
                                    'manager' => 'Project Manager / Lead',
                                    'engineer' => 'Engineer / Technical',
                                    'finance' => 'Finance / Accountant',
                                    'safety_officer' => 'Safety Officer',
                                ])
                                ->nullable()
                                ->native(false)
                                ->columnSpan(2),

                            Forms\Components\Select::make('assigned_user_id')
                                ->label('Specific Approver (Optional)')
                                ->options(fn() => User::where('company_id', auth()->user()?->company_id)
                                    ->where('is_active', true)
                                    ->pluck('name', 'id'))
                                ->searchable()
                                ->nullable()
                                ->columnSpan(2),
                        ])
                        ->columns(7)
                        ->orderColumn('step_sequence')
                        ->defaultItems(1)
                        ->addActionLabel('+ Add Step')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Hidden::make('company_id')
                ->default(fn() => auth()->user()?->company_id),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('module_type')
                    ->label('Module')
                    ->badge()
                    ->formatStateUsing(fn(string $state) => self::$moduleTypes[$state] ?? $state)
                    ->color('info'),

                Tables\Columns\TextColumn::make('steps_count')
                    ->label('Steps')
                    ->counts('steps')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('module_type')
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
            'index' => Pages\ListWorkflowTemplates::route('/'),
            'create' => Pages\CreateWorkflowTemplate::route('/create'),
            'edit' => Pages\EditWorkflowTemplate::route('/{record}/edit'),
        ];
    }
}
