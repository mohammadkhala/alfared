<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\KpiWidget;
use App\Filament\Widgets\SalesChartWidget;
use App\Filament\Widgets\LatestOrders;
use App\Filament\Widgets\TopProductsWidget;
use App\Filament\Widgets\VisitorsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
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
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('أبناء الفريد')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/logo.png'))
            ->colors([
                'primary' => Color::hex('#1B3B8C'),
                'warning' => Color::hex('#E8711A'),
                'success' => Color::Emerald,
                'danger'  => Color::Red,
                'info'    => Color::Blue,
            ])
            ->font('Cairo')

            // ── Database notifications (bell + polling every 10s) ─────────
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')

            // ── Custom CSS + sound for new notifications ─────────────────
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => '<link rel="stylesheet" href="' . asset('css/admin.css') . '">'
                    . '<script src="' . asset('js/admin-notifications.js') . '" defer></script>'
            )

            // ── Branded subheading above the login form ─────────────────
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn(): string => <<<'HTML'
                    <div style="text-align:center;margin:-8px 0 18px;">
                        <div style="font-size:13px;color:#64748B;font-weight:600;">
                            مرحباً بك في لوحة إدارة <span style="color:#E8711A;font-weight:800;">أبناء الفريد</span>
                        </div>
                    </div>
                HTML
            )

            // ── Branded footer note ─────────────────────────────────────
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn(): string => <<<'HTML'
                    <div style="margin-top:24px;text-align:center;padding-top:20px;border-top:1px solid #E5E7EB;">
                        <div style="font-size:11px;color:#94A3B8;letter-spacing:0.5px;">
                            🔒 الدخول محمي بتشفير من طرف إلى طرف
                        </div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:6px;">
                            للاستفسار: <a href="https://wa.me/970598191312" target="_blank" style="color:#25D366;text-decoration:none;">💬 واتساب</a>
                        </div>
                    </div>
                HTML
            )

            // ── Topbar: date + view site ─────────────────────────────────
            ->renderHook(
                PanelsRenderHook::TOPBAR_END,
                fn(): string =>
                    '<div style="display:flex;align-items:center;gap:12px;margin-inline-end:8px;">'
                    . '<span style="background:#F1F5F9;border-radius:8px;padding:6px 12px;font-size:12px;color:#64748B;font-weight:600;">📅 ' . now()->translatedFormat('d F Y') . '</span>'
                    . '<a href="https://alfared.ps" target="_blank" style="font-size:13px;color:#1B3B8C;text-decoration:none;font-weight:700;">🌐 عرض الموقع</a>'
                    . '</div>'
            )

            // ── Sidebar footer: current user ────────────────────────────
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                function (): string {
                    $user   = auth()->user();
                    if (! $user) return '';
                    $initial = mb_strtoupper(mb_substr($user->name, 0, 1));
                    $role    = match($user->role) { 'admin' => 'مدير عام', 'staff' => 'موظف', default => 'عميل' };
                    $name    = e($user->name);
                    return <<<HTML
                    <div style="padding:14px 16px;border-top:1px solid rgba(255,255,255,0.1);flex-shrink:0;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#E8711A;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:900;color:white;flex-shrink:0;">
                                {$initial}
                            </div>
                            <div style="overflow:hidden;">
                                <div style="font-size:12px;font-weight:700;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{$name}</div>
                                <div style="font-size:10px;color:#94A3B8;">{$role}</div>
                            </div>
                        </div>
                    </div>
                    HTML;
                }
            )

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                KpiWidget::class,
                VisitorsWidget::class,
                SalesChartWidget::class,
                LatestOrders::class,
                TopProductsWidget::class,
            ])
            ->navigationGroups([
                'المتجر',
                'المخزون',
                'التقارير',
                'الإعدادات',
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
            ->authMiddleware([Authenticate::class]);
    }
}
