<?php

namespace App\Filament\App\Pages;

use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use BackedEnum;
use UnitEnum;
use App\Support\StoragePath;

class CompanyBranding extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $title = 'Company Branding';
    protected static ?string $navigationLabel = 'Branding';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.app.pages.company-branding';

    public ?array $data = [];

    public function mount(): void
    {
        $company = auth()->user()?->company;

        if (!$company) {
            return;
        }

        $this->form->fill([
            'name' => $company->name,
            'logo' => $company->logo,
            'favicon' => $company->favicon,
            'primary_color' => $company->primary_color,
            'secondary_color' => $company->secondary_color,
            'email' => $company->email,
            'phone' => $company->phone,
            'website' => $company->website,
            'address' => $company->address,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand Identity')
                    ->description('Upload your company logo and set brand colors. These will appear in the panel sidebar, emails, and printed documents.')
                    ->icon('heroicon-o-sparkles')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\FileUpload::make('logo')
                                ->label('Company Logo')
                                ->image()
                                ->directory(StoragePath::companyAssets() . '/logos')
                                ->imageResizeMode('contain')
                                ->imageCropAspectRatio('497:228')
                                ->imageResizeTargetWidth('497')
                                ->imageResizeTargetHeight('228')
                                ->maxSize(2048)
                                ->helperText('Will be resized to 497×228px to match the standard sidebar logo size. PNG or JPEG recommended.')
                                ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp']),

                            Forms\Components\FileUpload::make('favicon')
                                ->label('Favicon')
                                ->image()
                                ->directory(StoragePath::companyAssets() . '/favicons')
                                ->imageResizeTargetWidth('64')
                                ->imageResizeTargetHeight('64')
                                ->helperText('32×32 or 64×64px (PNG or ICO). Shown in the browser tab.')
                                ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml']),
                        ]),

                        Grid::make(2)->schema([
                            Forms\Components\ColorPicker::make('primary_color')
                                ->label('Primary Brand Color')
                                ->helperText('Used for buttons, links, and accents across the panel and emails.'),

                            Forms\Components\ColorPicker::make('secondary_color')
                                ->label('Secondary Color')
                                ->helperText('Used for hover states and secondary UI elements.'),
                        ]),
                    ]),

                Section::make('Company Details')
                    ->description('These details appear in emails, invoices, and printed documents.')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Company Name')
                                ->required()
                                ->maxLength(255)
                                ->helperText('Displayed as the brand name in the sidebar.'),

                            Forms\Components\TextInput::make('website')
                                ->label('Website')
                                ->url()
                                ->maxLength(255)
                                ->prefix('https://')
                                ->helperText('Linked from company emails.'),
                        ]),

                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('Company Email')
                                ->email()
                                ->maxLength(255)
                                ->helperText('Shown in email footers and documents.'),

                            Forms\Components\TextInput::make('phone')
                                ->label('Phone')
                                ->maxLength(50),
                        ]),

                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(2)
                            ->helperText('Printed on invoices and official documents.'),
                    ])
                    ->collapsed(),

                Section::make('Preview')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Forms\Components\Placeholder::make('branding_preview')
                            ->label('')
                            ->content(function () {
                                $company = auth()->user()?->company;
                                if (!$company) {
                                    return 'No company found.';
                                }

                                $logoUrl = $company->getLogoUrl();
                                $name = $company->getBrandName();
                                $color = $company->primary_color ?? '#4f46e5';

                                $html = '<div style="background:#1e293b;border-radius:12px;padding:20px;display:flex;align-items:center;gap:16px;margin-bottom:12px;">';
                                if ($logoUrl) {
                                    $html .= '<img src="' . e($logoUrl) . '" alt="Logo" style="max-height:40px;max-width:180px;">';
                                } else {
                                    $html .= '<div style="background:' . e($color) . ';color:#fff;padding:8px 16px;border-radius:8px;font-weight:700;font-size:14px;">' . e($name) . '</div>';
                                }
                                $html .= '</div>';
                                $html .= '<p style="font-size:12px;color:#9ca3af;">This is how your brand appears in the sidebar. Upload a logo and set colors above, then save to see the change.</p>';

                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ])
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $company = auth()->user()?->company;

        if (!$company) {
            Notification::make()
                ->title('Error')
                ->body('No company found for your account.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        $company->update([
            'name' => $data['name'],
            'logo' => $data['logo'],
            'favicon' => $data['favicon'],
            'primary_color' => $data['primary_color'],
            'secondary_color' => $data['secondary_color'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'website' => $data['website'],
            'address' => $data['address'],
        ]);

        Notification::make()
            ->title('Branding Updated! ✨')
            ->body('Your company branding has been saved. Refresh the page to see the new logo and colors applied.')
            ->success()
            ->send();
    }
}
