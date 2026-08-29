<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\CustomEditProfile;
use App\Filament\Pages\Auth\CustomRegister;
use App\Http\Middleware\RedirectNonAdmins;
use App\Settings\GeneralSettings;
use App\Settings\ThemeSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
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
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $primaryColor = Color::Indigo;
        $fontFamily = 'Inter';
        $brandLogo = null;
        $brandName = 'CMMS';

        try {
            $themeSettings = rescue(fn () => app(ThemeSettings::class), report: false);
            if ($themeSettings?->primary_color) {
                $primaryColor = Color::hex($themeSettings->primary_color);
            }
            if ($themeSettings?->font_family) {
                $fontFamily = $themeSettings->font_family;
            }

            $generalSettings = rescue(fn () => app(GeneralSettings::class), report: false);
            if ($generalSettings?->site_logo) {
                $brandLogo = asset('storage/'.$generalSettings->site_logo);
            }
            if ($generalSettings?->site_name) {
                $brandName = 'Admin - '.$generalSettings->site_name;
            } else {
                $brandName = 'Admin - CMMS';
            }
        } catch (\Throwable $e) {
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->profile(CustomEditProfile::class, isSimple: false)
            ->login()
            ->registration(CustomRegister::class)
            ->favicon($brandLogo ? $brandLogo.'?v=1' : asset('assets/metronic/media/logos/favicon.ico'))
            ->colors([
                'primary' => $primaryColor,
            ])
            ->font($fontFamily)
            ->brandName($brandName)
            ->brandLogo(fn () => view('filament.logo', ['logo' => $brandLogo, 'name' => $brandName]))
            ->brandLogoHeight('2rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Buka Modul PLANT')
                    ->url(fn (): string => route('plt.dashboard'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->sort(9),
                MenuItem::make()
                    ->label('Buka Modul SCM')
                    ->url(fn (): string => route('scm.dashboard'))
                    ->icon('heroicon-o-shopping-bag')
                    ->sort(10),
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
                RedirectNonAdmins::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('@include("filament.theme-overrides")'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@livewire(\'floating-chat\')'),
            );
    }
}
