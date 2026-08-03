<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Http\Middleware\FilamentUserSettings;
use App\Http\Middleware\SessionSecurity;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Auth\MultiFactor\Email\EmailAuthentication;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Admin\Pages\Auth\Login::class)
            ->passwordReset()
            ->profile()
            ->multiFactorAuthentication(
                EmailAuthentication::make()
                    ->codeExpiryMinutes(10)
                    ->codeNotification(\App\Notifications\VerifyEmailAuthenticationNotification::class),
            )
            ->colors([
                'primary' => Color::Indigo,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Inter')
            ->darkMode(true)
            ->brandName('InfraHub Admin')
            ->brandLogo(asset('logo/infrahub-logo-light.png'))
            ->darkModeBrandLogo(asset('logo/infrahub-logo-dark.png'))
            ->brandLogoHeight('2.5rem')
            ->spa()
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Pages\SystemSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
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
                SessionSecurity::class,
                \App\Http\Middleware\ForcePasswordChange::class,
                FilamentUserSettings::class,
            ])
            ->navigationGroups([
                'Platform Management',
                'Subscription & Billing',
                'Settings',
                'System',
            ])
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn() => view('components.loading-overlay'),
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
                    <link rel="stylesheet" href="/css/offline.css">
                '),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn() => new \Illuminate\Support\HtmlString('
                    <script src="/js/offline-db.js"></script>
                    <script src="/js/offline-ui.js"></script>
                    <script>
                        if ("serviceWorker" in navigator) {
                            window.addEventListener("load", () => {
                                navigator.serviceWorker.register("/sw.js")
                                    .then(reg => {
                                        console.log("SW registered:", reg.scope);
                                        if ("sync" in reg) {
                                            reg.sync.register("infrahub-sync").catch(() => {});
                                        }
                                    })
                                    .catch(err => console.warn("SW registration failed:", err));
                            });
                        }
                    </script>
                '),
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn() => view('components.pwa-install-prompt'),
            )
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}