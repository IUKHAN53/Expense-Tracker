<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * The end-user Filament panel at /app. Regular signed-up users land here
 * (the /admin panel is SuperAdmin-only). Same resources are discovered;
 * the AccountScope global filter handles tenant isolation.
 */
class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->passwordReset()
            // Web signup is intentionally disabled for v1 — users register
            // via the mobile app's POST /api/register endpoint. Add a
            // custom register page that calls AccountProvisioner if web
            // signup is needed later.
            ->brandName('Kharcha')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.1rem')
            ->favicon(asset('img/kharcha-mark.svg'))
            ->darkMode(false)
            ->font('Geist')
            ->colors([
                'primary' => Color::hex('#c9621f'),
                'gray' => Color::hex('#7c6a52'),
                'success' => Color::hex('#5d7a3d'),
                'danger' => Color::hex('#b14430'),
                'warning' => Color::hex('#c9621f'),
                'info' => Color::hex('#5d7a3d'),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="preconnect" href="https://fonts.googleapis.com">'
                    .'<link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,500;1,400;1,500&family=Geist+Mono:wght@400;500&display=swap" rel="stylesheet">'
                    .'<link rel="stylesheet" href="'.asset('css/kharcha-filament.css').'">',
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
