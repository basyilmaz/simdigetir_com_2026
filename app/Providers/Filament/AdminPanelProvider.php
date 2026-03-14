<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            ->brandName('SimdiGetir Yönetim')
            ->colors([
                'primary' => Color::Orange,
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->navigationGroups([
                'Müşteri Talepleri',
                'Operasyon',
                'Finans',
                'Büyüme',
                'Sayfa Yönetimi',
                'Reklam Platformu',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverResources(
                in: base_path('Modules/Landing/app/Filament/Resources'),
                for: 'Modules\\Landing\\Filament\\Resources'
            )
            ->discoverResources(
                in: base_path('Modules/AdsCore/app/Filament/Resources'),
                for: 'Modules\\AdsCore\\Filament\\Resources'
            )
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverPages(
                in: base_path('Modules/Landing/app/Filament/Pages'),
                for: 'Modules\\Landing\\Filament\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ]);

        if (config('admin_panel.enable_optional_2fa', false) && method_exists($panel, 'requiresTwoFactorAuthentication')) {
            $panel = $panel->requiresTwoFactorAuthentication();
        }

        return $panel
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
            ]);
    }
}
