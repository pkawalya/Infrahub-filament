<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use App\Support\UserManualHelper;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditUserManual extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $title = 'User Manual Editor';
    protected static ?int $navigationSort = 21;
    protected string $view = 'filament.admin.pages.edit-user-manual';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'sections' => UserManualHelper::getSections(),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Manage User Manual Sections')
                    ->description('Organize the manual into distinct sections. For each section, provide a title, group, navigation icon, markdown content, and an optional screenshot.')
                    ->schema([
                        Repeater::make('sections')
                            ->schema([
                                Select::make('group')
                                    ->label('Navigation Group')
                                    ->options([
                                        'Introduction' => 'Introduction',
                                        'Operations' => 'Operations',
                                        'Site & Resources' => 'Site & Resources',
                                        'Commercial & Cost' => 'Commercial & Cost',
                                        'Collaboration' => 'Collaboration',
                                    ])
                                    ->required()
                                    ->columnSpan(1),
                                TextInput::make('title')
                                    ->label('Section Title')
                                    ->placeholder('e.g. Project Schedule & Tasks')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('icon')
                                    ->label('Navigation Icon (Emoji)')
                                    ->placeholder('e.g. 🧭, 📅, 📦')
                                    ->required()
                                    ->columnSpan(1),
                                MarkdownEditor::make('content')
                                    ->label('Content (Markdown)')
                                    ->required()
                                    ->columnSpanFull(),
                                FileUpload::make('image_path')
                                    ->label('Section Image (Optional)')
                                    ->directory('manual-images')
                                    ->visibility('public')
                                    ->preserveFilenames()
                                    ->image()
                                    ->columnSpanFull()
                                    ->helperText('Upload a screenshot/image for this section.'),
                            ])
                            ->columns(4)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => ($state['title'] ?? null) ? ($state['icon'] ?? '') . ' ' . $state['title'] : 'New Section')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::setValue('user_manual_sections', $state['sections'] ?? [], 'manual');

        Notification::make()
            ->title('User Manual Saved')
            ->body('Manual sections and images updated successfully.')
            ->success()
            ->send();
    }
}
