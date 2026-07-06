<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
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
        $defaultMarkdown = '';
        $path = base_path('USER_MANUAL.md');
        if (file_exists($path)) {
            $defaultMarkdown = file_get_contents($path);
        }

        $imagesValue = Setting::getValue('user_manual_images', []);
        $images = is_string($imagesValue) ? json_decode($imagesValue, true) : $imagesValue;
        if (!is_array($images)) {
            $images = [];
        }

        $this->form->fill([
            'markdown' => Setting::getValue('user_manual_markdown', $defaultMarkdown),
            'images' => $images,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Edit Manual Content')
                    ->description('Modify the end-user and site operations manual. Supports Markdown format.')
                    ->schema([
                        MarkdownEditor::make('markdown')
                            ->label('Manual (Markdown)')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Upload & Manage Manual Images')
                    ->description('Upload images to reference in the manual. After uploading, copy their URLs to embed in the markdown above.')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Images')
                            ->multiple()
                            ->directory('manual-images')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->reorderable()
                            ->columnSpanFull()
                            ->helperText('Allowed formats: png, jpg, jpeg, gif, svg. Max size: 5MB.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::setValue('user_manual_markdown', $state['markdown'], 'manual');
        Setting::setValue('user_manual_images', $state['images'] ?? [], 'manual');

        Notification::make()
            ->title('User Manual Saved')
            ->body('Manual content and image references updated successfully.')
            ->success()
            ->send();
    }
}
