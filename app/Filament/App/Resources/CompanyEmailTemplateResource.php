<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CompanyEmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class CompanyEmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Email Templates';
    protected static ?string $modelLabel = 'Email Template';
    protected static ?string $slug = 'email-templates';

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user && $user->company_id) {
            // Company admins see their company templates + global templates (read-only reference)
            $query->where(function ($q) use ($user) {
                $q->where('company_id', $user->company_id)
                    ->orWhereNull('company_id');
            });
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Template Details')
                ->description('Define the email template content.')
                ->icon('heroicon-o-envelope')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Project Assignment Notification'),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Unique identifier. e.g. project-assignment')
                        ->placeholder('project-assignment'),

                    Forms\Components\Select::make('category')
                        ->options(EmailTemplate::categories())
                        ->required()
                        ->default('general')
                        ->live()
                        ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                            $vars = EmailTemplate::variablesForCategory($state);
                            $set('available_variables', array_keys($vars));
                        }),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])->columns(2),

            Schemas\Components\Section::make('Email Content')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('subject')
                        ->required()
                        ->maxLength(500)
                        ->helperText('Use {{variable_name}} for dynamic content.')
                        ->placeholder('Welcome to {{company_name}}, {{user_name}}!'),

                    Forms\Components\RichEditor::make('body')
                        ->required()
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'h2',
                            'h3',
                            'bulletList',
                            'orderedList',
                            'link',
                            'blockquote',
                            'codeBlock',
                            'redo',
                            'undo',
                        ])
                        ->helperText('Use {{variable_name}} placeholders for dynamic content.')
                        ->columnSpanFull(),
                ]),

            Schemas\Components\Section::make('Available Variables')
                ->description('Select variables you plan to use. These act as documentation for the template.')
                ->icon('heroicon-o-code-bracket')
                ->schema([
                    Forms\Components\CheckboxList::make('available_variables')
                        ->options(function (\Filament\Schemas\Components\Utilities\Get $get) {
                            $category = $get('category') ?? 'general';
                            return EmailTemplate::variablesForCategory($category);
                        })
                        ->columns(3)
                        ->gridDirection('row')
                        ->bulkToggleable(),
                ])
                ->collapsed(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Template Details')->schema([
                Infolists\Components\TextEntry::make('name')->icon('heroicon-o-envelope'),
                Infolists\Components\TextEntry::make('slug')->copyable()->icon('heroicon-o-code-bracket'),
                Infolists\Components\TextEntry::make('category')->badge()->color('primary'),
                Infolists\Components\IconEntry::make('is_active')->boolean()->label('Active'),
                Infolists\Components\TextEntry::make('company.name')->label('Scope')
                    ->placeholder('Global (System)')
                    ->badge()
                    ->color(fn(EmailTemplate $record) => $record->isGlobal() ? 'success' : 'warning'),
            ])->columns(3),

            Schemas\Components\Section::make('Email Content')->schema([
                Infolists\Components\TextEntry::make('subject')->columnSpanFull(),
                Infolists\Components\TextEntry::make('body')->html()->columnSpanFull(),
            ]),

            Schemas\Components\Section::make('Variables')->schema([
                Infolists\Components\TextEntry::make('available_variables')
                    ->label('Available Merge Fields')
                    ->getStateUsing(
                        fn(EmailTemplate $record) =>
                        $record->available_variables
                        ? collect($record->available_variables)->map(fn($v) => "{{" . $v . "}}")->join(', ')
                        : 'None'
                    )
                    ->columnSpanFull(),
            ])->collapsed(),

            Schemas\Components\Section::make('Metadata')->schema([
                Infolists\Components\TextEntry::make('creator.name')->label('Created By')->placeholder('System'),
                Infolists\Components\TextEntry::make('created_at')->dateTime(),
                Infolists\Components\TextEntry::make('updater.name')->label('Last Updated By')->placeholder('—'),
                Infolists\Components\TextEntry::make('updated_at')->dateTime(),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(EmailTemplate $record) => $record->slug),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scope')
                    ->label('Scope')
                    ->getStateUsing(fn(EmailTemplate $record) => $record->isGlobal() ? 'Global' : 'Company')
                    ->badge()
                    ->color(fn(EmailTemplate $record) => $record->isGlobal() ? 'success' : 'warning'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Modified'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(EmailTemplate::categories()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->visible(fn(EmailTemplate $record) => !$record->isGlobal()),
                Actions\Action::make('override')
                    ->label('Customize')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn(EmailTemplate $record) => $record->isGlobal())
                    ->requiresConfirmation()
                    ->modalHeading('Create Company Override')
                    ->modalDescription('This will create a company-specific copy of this global template that you can customize.')
                    ->action(function (EmailTemplate $record) {
                        $user = auth()->user();
                        $clone = $record->replicate();
                        $clone->company_id = $user->company_id;
                        $clone->name = $record->name . ' (Custom)';
                        $clone->created_by = $user->id;
                        $clone->updated_by = $user->id;
                        $clone->save();
                    }),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make()
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyEmailTemplates::route('/'),
            'create' => Pages\CreateCompanyEmailTemplate::route('/create'),
            'view' => Pages\ViewCompanyEmailTemplate::route('/{record}'),
            'edit' => Pages\EditCompanyEmailTemplate::route('/{record}/edit'),
        ];
    }
}
