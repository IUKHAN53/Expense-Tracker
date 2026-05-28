<?php

namespace App\Providers\Filament;

use App\Filament\SuperAdmin\Pages\Dashboard;
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
 * God-mode panel at /superadmin. Restricted to SuperAdmins (see
 * User::canAccessPanel). It reuses the same tenant resources as /admin and
 * /app — but because a SuperAdmin bypasses AccountScope, those resources
 * surface every tenant's data here, with the cross-tenant "Household"
 * columns/filters the resource tables already expose. On top of that it
 * registers a dedicated cross-tenant dashboard and platform report widgets
 * (discovered from app/Filament/SuperAdmin), which the per-account panels
 * never see.
 */
class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('superadmin')
            ->path('superadmin')
            ->login()
            ->brandName('Kharcha · Super')
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
            ->discoverPages(in: app_path('Filament/SuperAdmin/Pages'), for: 'App\Filament\SuperAdmin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/SuperAdmin/Widgets'), for: 'App\Filament\SuperAdmin\Widgets')
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
