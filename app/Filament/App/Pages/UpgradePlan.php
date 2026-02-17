<?php

namespace App\Filament\App\Pages;

use App\Models\Setting;
use App\Models\Subscription;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class UpgradePlan extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Upgrade Plan';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 99;
    protected static ?string $title = 'Upgrade Your Plan';
    protected static ?string $slug = 'settings/upgrade';
    protected string $view = 'filament.app.pages.upgrade-plan';

    // Livewire properties
    public int $extraUsers = 0;
    public int $extraProjects = 0;
    public int $extraStorage = 0;
    public ?int $selectedPlanId = null;

    public function mount(): void
    {
        $company = auth()->user()?->company;
        if ($company) {
            $this->extraUsers = (int) $company->extra_users;
            $this->extraProjects = (int) $company->extra_projects;
            $this->extraStorage = (int) $company->extra_storage_gb;
            $this->selectedPlanId = $company->subscription_id;
        }
    }

    public function getViewData(): array
    {
        $company = auth()->user()?->company;
        $currentPlan = $company?->subscription;
        $plans = Subscription::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get();

        $addonPricing = [
            'price_per_extra_user' => (float) Setting::getValue('price_per_extra_user', '5.00'),
            'price_per_extra_project' => (float) Setting::getValue('price_per_extra_project', '10.00'),
            'price_per_extra_gb' => (float) Setting::getValue('price_per_extra_gb', '2.00'),
            'billing_cycle' => Setting::getValue('addon_billing_cycle', 'monthly'),
            'currency' => Setting::getValue('addon_currency', 'USD'),
            'currency_symbol' => Setting::getValue('addon_currency_symbol', '$'),
        ];

        return [
            'company' => $company,
            'currentPlan' => $currentPlan,
            'plans' => $plans,
            'addonPricing' => $addonPricing,
        ];
    }

    /**
     * Switch to a different subscription plan.
     */
    public function switchPlan(int $planId): void
    {
        $company = auth()->user()?->company;
        if (!$company)
            return;

        $plan = Subscription::find($planId);
        if (!$plan || !$plan->is_active) {
            Notification::make()->danger()->title('Invalid plan')->send();
            return;
        }

        // Check if downgrading would violate current usage
        $userCount = $company->users()->count();
        $projectCount = $company->projects()->count();

        $effectiveUsers = $plan->max_users + $this->extraUsers;
        $effectiveProjects = $plan->max_projects + $this->extraProjects;

        if ($userCount > $effectiveUsers) {
            Notification::make()
                ->danger()
                ->title('Cannot switch to this plan')
                ->body("You currently have {$userCount} users but this plan only supports {$effectiveUsers} (incl. addons). Remove users first or add more extra users.")
                ->persistent()
                ->send();
            return;
        }

        if ($projectCount > $effectiveProjects) {
            Notification::make()
                ->danger()
                ->title('Cannot switch to this plan')
                ->body("You currently have {$projectCount} projects but this plan only supports {$effectiveProjects} (incl. addons). Remove projects first or add more extra projects.")
                ->persistent()
                ->send();
            return;
        }

        $company->applyPlan($plan, $company->billing_cycle ?? 'monthly');
        $this->selectedPlanId = $plan->id;

        Notification::make()
            ->success()
            ->title("Switched to {$plan->name}")
            ->body('Your plan has been updated successfully.')
            ->send();
    }

    /**
     * Apply extra addon resources.
     */
    public function applyAddons(): void
    {
        $company = auth()->user()?->company;
        if (!$company)
            return;

        $company->update([
            'extra_users' => max(0, $this->extraUsers),
            'extra_projects' => max(0, $this->extraProjects),
            'extra_storage_gb' => max(0, $this->extraStorage),
        ]);

        Notification::make()
            ->success()
            ->title('Addons updated')
            ->body('Your extra resources have been applied. New limits are active immediately.')
            ->send();
    }
}
