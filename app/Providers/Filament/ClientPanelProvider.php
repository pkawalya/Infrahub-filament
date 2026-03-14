<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\SessionSecurity;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')
            ->login(\App\Filament\Client\Pages\Auth\Login::class)
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->multiFactorAuthentication(
                EmailAuthentication::make()
                    ->codeExpiryMinutes(10)
                    ->codeNotification(\App\Notifications\VerifyEmailAuthenticationNotification::class),
            )
            ->colors([
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
            ])
            ->brandName('InfraHub — Client Portal')
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
            ->pages([
                \App\Filament\Client\Pages\Dashboard::class,
            ])
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
                ForcePasswordChange::class,
            ])
            ->databaseNotifications()
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn() => new \Illuminate\Support\HtmlString('
                    <link rel="manifest" href="/manifest.json">
                    <meta name="theme-color" content="#6366f1">
                '),
            );
    }
}
