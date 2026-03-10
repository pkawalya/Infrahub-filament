<?php

namespace App\Filament\App\Pages;

use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CompanyConfigOptions extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Dropdown Options';
    protected static ?string $title = 'Configurable Options';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.app.pages.company-config-options';

    public ?array $data = [];

    public function mount(): void
    {
        $company = auth()->user()->company;

        // Merge existing overrides with defaults for display
        $options = $company->configurable_options ?? [];

        $this->form->fill([
            'weather_types' => $this->arrayToTagList($company->getOptions('weather_types')),
            'subcontractor_specialties' => $this->arrayToTagList($company->getOptions('subcontractor_specialties')),
            'tender_categories' => $this->arrayToTagList($company->getOptions('tender_categories')),
            'tender_sources' => $this->arrayToTagList($company->getOptions('tender_sources')),
            'attendance_statuses' => $this->arrayToTagList($company->getOptions('attendance_statuses')),
            'asset_categories' => $this->arrayToTagList($company->getOptions('asset_categories')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Weather Types')
                ->description('Options available in the site diary weather dropdown.')
                ->icon('heroicon-o-sun')
                ->schema([
                    Forms\Components\TagsInput::make('data.weather_types')
                        ->label('')
                        ->placeholder('Add weather type')
                        ->helperText('These appear in the Daily Site Diary weather dropdown.'),
                ])->collapsed(),

            Section::make('Subcontractor Specialties')
                ->description('Trade specialties for subcontractor classification.')
                ->icon('heroicon-o-user-group')
                ->schema([
                    Forms\Components\TagsInput::make('data.subcontractor_specialties')
                        ->label('')
                        ->placeholder('Add specialty'),
                ])->collapsed(),

            Section::make('Tender Categories')
                ->description('Project types for tender classification.')
                ->icon('heroicon-o-document-magnifying-glass')
                ->schema([
                    Forms\Components\TagsInput::make('data.tender_categories')
                        ->label('')
                        ->placeholder('Add category'),
                ])->collapsed(),

            Section::make('Tender Sources')
                ->description('Where tenders are sourced from.')
                ->icon('heroicon-o-globe-alt')
                ->schema([
                    Forms\Components\TagsInput::make('data.tender_sources')
                        ->label('')
                        ->placeholder('Add source'),
                ])->collapsed(),

            Section::make('Attendance Statuses')
                ->description('Status options for crew attendance tracking.')
                ->icon('heroicon-o-clock')
                ->schema([
                    Forms\Components\TagsInput::make('data.attendance_statuses')
                        ->label('')
                        ->placeholder('Add status'),
                ])->collapsed(),

            Section::make('Asset Categories')
                ->description('Equipment/asset classification categories.')
                ->icon('heroicon-o-server-stack')
                ->schema([
                    Forms\Components\TagsInput::make('data.asset_categories')
                        ->label('')
                        ->placeholder('Add category'),
                ])->collapsed(),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $company = auth()->user()->company;

        // Convert tag lists back to key=>value arrays
        $options = [];

        foreach (['weather_types', 'subcontractor_specialties', 'tender_categories', 'tender_sources', 'attendance_statuses', 'asset_categories'] as $key) {
            $tags = $data[$key] ?? null;
            if ($tags && is_array($tags)) {
                $mapped = [];
                foreach ($tags as $tag) {
                    $slug = \Illuminate\Support\Str::slug($tag, '_');
                    $mapped[$slug] = $tag;
                }
                // Only store if different from defaults
                if ($mapped !== (Company::$defaultOptions[$key] ?? [])) {
                    $options[$key] = $mapped;
                }
            }
        }

        $company->update([
            'configurable_options' => !empty($options) ? $options : null,
        ]);

        Notification::make()
            ->title('Options saved')
            ->success()
            ->body('Dropdown options have been updated for your company.')
            ->send();
    }

    /**
     * Convert associative options array to a simple values list for TagsInput.
     */
    private function arrayToTagList(array $options): array
    {
        return array_values($options);
    }
}
