<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static string|UnitEnum|null $navigationGroup = 'Platform Management';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Email Templates';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Template Details')
                ->description('Define the email template content and settings.')
                ->icon('heroicon-o-envelope')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. Welcome Email'),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true, modifyRuleUsing: function ($rule, $record) {
                            if ($record) {
                                return $rule->where('company_id', $record->company_id);
                            }
                            return $rule->whereNull('company_id');
                        })
                        ->helperText('Unique identifier used in code. e.g. welcome-email')
                        ->placeholder('welcome-email'),

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
                        ->helperText('Use {{variable_name}} for dynamic content. e.g. Welcome to {{app_name}}, {{user_name}}!')
                        ->placeholder('Welcome to {{app_name}}, {{user_name}}!'),

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
                ->description('Variables that can be used in the subject and body with {{variable_name}} syntax.')
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
                    ->placeholder('Global (All Companies)')
                    ->badge()->color('warning'),
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
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Scope')
                    ->placeholder('Global')
                    ->badge()
                    ->color(fn(EmailTemplate $record) => $record->isGlobal() ? 'success' : 'warning')
                    ->sortable(),
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
                Tables\Filters\TernaryFilter::make('scope')
                    ->label('Scope')
                    ->placeholder('All')
                    ->trueLabel('Global Only')
                    ->falseLabel('Company Only')
                    ->queries(
                        true: fn($query) => $query->whereNull('company_id'),
                        false: fn($query) => $query->whereNotNull('company_id'),
                    ),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Template')
                    ->modalDescription('This will create a copy of this template.')
                    ->action(function (EmailTemplate $record) {
                        $clone = $record->replicate();
                        $clone->name = $record->name . ' (Copy)';
                        $clone->slug = $record->slug . '-copy-' . now()->timestamp;
                        $clone->created_by = auth()->id();
                        $clone->save();
                    }),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'view' => Pages\ViewEmailTemplate::route('/{record}'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
