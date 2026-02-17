<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Dashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Http\Middleware\FilamentUserSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
                'primary' => Color::Blue,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Inter')
            ->darkMode(true)
            ->brandName('InfraHub')
            ->brandLogo(asset('logo/infrahub-logo-new.png'))
            ->darkModeBrandLogo(asset('logo/infrahub-logo-new.png'))
            ->brandLogoHeight('2.5rem')
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Pages\SystemSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                FilamentUserSettings::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->navigationGroups([
                'Dashboard',
                'Projects',
                'Company',
                'Settings',
            ])
            ->globalSearch(false)
            ->renderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
                fn() => view('filament.app.components.project-selector'),
            )
            ->databaseNotifications()
            ->databaseTransactions()
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn() => view('components.loading-overlay'),
            )
            ->viteTheme('resources/css/filament/app/theme.css');
    }
}
