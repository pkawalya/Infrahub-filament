<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AddonPricing extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Addon Pricing';
    protected static string|\UnitEnum|null $navigationGroup = 'Platform Settings';
    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Addon Pricing';
    protected static ?string $slug = 'addon-pricing';
    protected string $view = 'filament.admin.pages.addon-pricing';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'price_per_extra_user' => Setting::getValue('price_per_extra_user', '5.00'),
            'price_per_extra_project' => Setting::getValue('price_per_extra_project', '10.00'),
            'price_per_extra_gb' => Setting::getValue('price_per_extra_gb', '2.00'),
            'addon_billing_cycle' => Setting::getValue('addon_billing_cycle', 'monthly'),
            'addon_currency' => Setting::getValue('addon_currency', 'USD'),
            'addon_currency_symbol' => Setting::getValue('addon_currency_symbol', '$'),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Per-Unit Addon Pricing')
                    ->description('Set the prices that companies will pay for resources beyond their plan limits. These prices are displayed on the Upgrade page.')
                    ->schema([
                        TextInput::make('addon_currency')
                            ->label('Currency Code')
                            ->placeholder('USD')
                            ->maxLength(3)
                            ->required(),
                        TextInput::make('addon_currency_symbol')
                            ->label('Currency Symbol')
                            ->placeholder('$')
                            ->maxLength(5)
                            ->required(),
                        Select::make('addon_billing_cycle')
                            ->label('Billing Cycle')
                            ->options([
                                'monthly' => 'Per Month',
                                'yearly' => 'Per Year',
                                'one_time' => 'One-Time',
                            ])
                            ->required(),
                    ])->columns(3),

                Section::make('Resource Prices')
                    ->schema([
                        TextInput::make('price_per_extra_user')
                            ->label('Price per Extra User')
                            ->numeric()
                            ->prefix(fn($get) => $get('addon_currency_symbol') ?: '$')
                            ->suffix(fn($get) => '/' . match ($get('addon_billing_cycle')) {
                                'yearly' => 'yr',
                                'one_time' => 'once',
                                default => 'mo',
                            })
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->helperText('Charged for each additional user beyond the plan limit'),
                        TextInput::make('price_per_extra_project')
                            ->label('Price per Extra Project')
                            ->numeric()
                            ->prefix(fn($get) => $get('addon_currency_symbol') ?: '$')
                            ->suffix(fn($get) => '/' . match ($get('addon_billing_cycle')) {
                                'yearly' => 'yr',
                                'one_time' => 'once',
                                default => 'mo',
                            })
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->helperText('Charged for each additional project beyond the plan limit'),
                        TextInput::make('price_per_extra_gb')
                            ->label('Price per Extra GB of Storage')
                            ->numeric()
                            ->prefix(fn($get) => $get('addon_currency_symbol') ?: '$')
                            ->suffix(fn($get) => '/' . match ($get('addon_billing_cycle')) {
                                'yearly' => 'yr',
                                'one_time' => 'once',
                                default => 'mo',
                            })
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->helperText('Charged for each additional GB of storage beyond the plan limit'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::setValue('price_per_extra_user', $data['price_per_extra_user'], 'addon_pricing');
        Setting::setValue('price_per_extra_project', $data['price_per_extra_project'], 'addon_pricing');
        Setting::setValue('price_per_extra_gb', $data['price_per_extra_gb'], 'addon_pricing');
        Setting::setValue('addon_billing_cycle', $data['addon_billing_cycle'], 'addon_pricing');
        Setting::setValue('addon_currency', $data['addon_currency'], 'addon_pricing');
        Setting::setValue('addon_currency_symbol', $data['addon_currency_symbol'], 'addon_pricing');

        Notification::make()
            ->success()
            ->title('Addon pricing saved')
            ->body('Changes will be reflected on company upgrade pages immediately.')
            ->send();
    }
}
