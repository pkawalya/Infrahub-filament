<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Colors\Color;
use Filament\Facades\Filament;
use App\Support\ColorPalette;
use App\Support\ContentWidthOptions;

class FilamentUserSettings
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $userId = $user->id;

            // ── 1. User color preference ─────────────────────────
            $savedColor = $this->getSavedColor($userId);

            // ── 2. Company branding override ─────────────────────
            $company = $user->company;

            if ($company && $company->primary_color) {
                // Company brand color takes priority
                FilamentColor::register([
                    'primary' => Color::hex($company->primary_color),
                ]);
            } else {
                // Fall back to user's personal color preference
                FilamentColor::register([
                    'primary' => $this->getColorConstant($savedColor),
                ]);
            }

            // ── 3. Company logo branding ─────────────────────────
            if ($company) {
                $this->applyCompanyBranding($company);
            }

            // ── 4. Navigation style ──────────────────────────────
            $this->setNavigationStyle($userId);

            // ── 5. Content width ─────────────────────────────────
            $this->setContentWidth($userId);

            // ── 6. Share branding with views ─────────────────────
            view()->share('companyBranding', $company ? $company->getBranding() : []);
        }

        return $next($request);
    }

    private function applyCompanyBranding($company): void
    {
        $panel = Filament::getCurrentPanel();
        if (!$panel) {
            return;
        }

        // Apply company logo if uploaded
        $logoUrl = $company->getLogoUrl();
        if ($logoUrl) {
            $panel->brandLogo($logoUrl);
            $panel->darkModeBrandLogo($logoUrl);
            $panel->brandLogoHeight('2.5rem');
        }

        // Apply company name as brand name
        if ($company->name) {
            $panel->brandName($company->name);
        }

        // Apply favicon if uploaded
        $faviconUrl = $company->getFaviconUrl();
        if ($faviconUrl) {
            $panel->favicon($faviconUrl);
        }
    }

    private function getSavedColor($userId): string
    {
        try {
            return Setting::getUserValue('filament_primary_color', 'blue', $userId);
        } catch (\Exception $e) {
            return 'blue';
        }
    }

    private function setNavigationStyle($userId): void
    {
        try {
            $navigationStyle = Setting::getUserValue('filament_navigation_style', 'sidebar', $userId);

            $panel = Filament::getCurrentPanel();

            if ($panel) {
                if ($navigationStyle === 'top') {
                    $panel->topNavigation();
                } else {
                    $panel->sidebarCollapsibleOnDesktop(true);
                }
            }
        } catch (\Exception $e) {
            // default
        }
    }

    private function getColorConstant(string $colorName)
    {
        return ColorPalette::constantFor($colorName);
    }

    private function setContentWidth($userId): void
    {
        try {
            $savedWidth = Setting::getUserValue('filament_content_width', '7xl', $userId);

            $panel = Filament::getCurrentPanel();

            if ($panel) {
                $widthEnum = ContentWidthOptions::widthEnum($savedWidth);
                $panel->maxContentWidth($widthEnum);
            }
        } catch (\Exception $e) {
            // default — Filament uses 7xl by default
        }
    }
}