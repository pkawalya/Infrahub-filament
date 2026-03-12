<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Dashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Http\Middleware\FilamentUserSettings;
use App\Http\Middleware\ForcePasswordChange;
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

use Filament\Auth\MultiFactor\Email\EmailAuthentication;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->login(\App\Filament\App\Pages\Auth\Login::class)
            // Registration disabled — users are created by company/super admins only
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->multiFactorAuthentication(
                EmailAuthentication::make()
                    ->codeExpiryMinutes(10)
                    ->codeNotification(\App\Notifications\VerifyEmailAuthenticationNotification::class),
            )
            ->colors([
                'primary' => Color::hex('#1d4ed8'),   // Professional Blue (Aconex style)
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Inter')
            ->darkMode(true)
            ->brandName('InfraHub')
            ->brandLogo(asset('logo/infrahub-logo-light.png'))
            ->darkModeBrandLogo(asset('logo/infrahub-logo-dark.png'))
            ->brandLogoHeight('2.5rem')
            ->spa()
            ->topNavigation()
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
                ForcePasswordChange::class,
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
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn() => view('components.fullscreen-toggle'),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn() => new \Illuminate\Support\HtmlString('
                    <link rel="manifest" href="/manifest.json">
                    <meta name="theme-color" content="#6366f1">
                    <meta name="apple-mobile-web-app-capable" content="yes">
                    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
                    <meta name="apple-mobile-web-app-title" content="InfraHub">
                    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">
                '),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn() => new \Illuminate\Support\HtmlString('
                    <script>
                        if ("serviceWorker" in navigator) {
                            window.addEventListener("load", () => {
                                navigator.serviceWorker.register("/sw.js")
                                    .then(reg => console.log("SW registered:", reg.scope))
                                    .catch(err => console.warn("SW registration failed:", err));
                            });
                        }
                    </script>
                '),
            )
            ->viteTheme('resources/css/filament/app/theme.css');
    }
}
