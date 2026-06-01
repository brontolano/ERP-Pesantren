<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ErpPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('erp')
            ->path('/')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('ERP Pesantren Asy-Syifaa')
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->darkModeBrandLogo(fn () => view('filament.brand-logo-dark'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.png'))
            ->authGuard('erp')
            ->colors([
                'primary' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Green,
                'info' => Color::Sky,
            ])
            ->navigationGroups([
                NavigationGroup::make('Dashboard')
                    ->icon('heroicon-o-squares-2x2'),
                NavigationGroup::make('Kepesantrenan')
                    ->icon('heroicon-o-academic-cap'),
                NavigationGroup::make('Keuangan')
                    ->icon('heroicon-o-banknotes'),
                NavigationGroup::make('SPMB')
                    ->icon('heroicon-o-clipboard-document-list'),
                NavigationGroup::make('CMS Website')
                    ->icon('heroicon-o-globe-alt')
                    ->collapsed(),
                NavigationGroup::make('Notifikasi')
                    ->icon('heroicon-o-bell')
                    ->collapsed(),
                NavigationGroup::make('Pengguna')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make('Pengaturan')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )
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
                \App\Http\Middleware\RedirectWaliToPwa::class,
                \App\Http\Middleware\ForcePasswordChange::class,
            ])
            ->viteTheme('resources/css/filament/theme.css')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->databaseNotifications()
            ->renderHook(
                'panels::auth.login.form.before',
                fn () => view('filament.login-header'),
            );
    }
}
