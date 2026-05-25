<?php

namespace App\Filament\Pages;

use App\Models\SiteVisit;
use Filament\Pages\Page;

class VisitorAnalytics extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'الزوار والإحصائيات';
    protected static ?string $title           = 'تحليلات الزوار';
    protected static ?string $navigationGroup = 'التقارير';
    protected static ?int    $navigationSort  = 3;
    protected static string  $view            = 'filament.pages.visitor-analytics';

    public string $period = 'today';   // today | week | month | all

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('view_sales_reports')
            ?? (auth()->user()?->role === 'admin');
    }

    public function setPeriod(string $period): void
    {
        $this->period = in_array($period, ['today', 'week', 'month', 'all'], true) ? $period : 'today';
    }

    protected function range(): array
    {
        return match($this->period) {
            'today' => [now()->startOfDay(),       now()->endOfDay()],
            'week'  => [now()->startOfWeek(),      now()->endOfWeek()],
            'month' => [now()->startOfMonth(),     now()->endOfMonth()],
            'all'   => [now()->subYears(10),       now()->endOfDay()],
        };
    }

    protected function getViewData(): array
    {
        [$from, $to] = $this->range();

        $base = SiteVisit::whereBetween('visited_at', [$from, $to]);

        $totalVisits  = (clone $base)->count();
        $uniqueVisits = (clone $base)->distinct('visitor_id')->count('visitor_id');
        $loggedIn     = (clone $base)->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // ── By device type ──
        $byDevice = (clone $base)
            ->selectRaw('device_type, COUNT(*) as total, COUNT(DISTINCT visitor_id) as uniques')
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get()
            ->keyBy('device_type');

        // ── Top pages ──
        $topPages = (clone $base)
            ->selectRaw('path, COUNT(*) as total, COUNT(DISTINCT visitor_id) as uniques')
            ->whereNotNull('path')
            ->groupBy('path')
            ->orderByDesc('total')
            ->limit(15)
            ->get();

        // ── Top browsers ──
        $topBrowsers = (clone $base)
            ->selectRaw('browser, COUNT(*) as total')
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ── Top OS ──
        $topOS = (clone $base)
            ->selectRaw('os, COUNT(*) as total')
            ->whereNotNull('os')
            ->groupBy('os')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ── Visits per day (for chart) ──
        $isToday = $this->period === 'today';
        $byTime = (clone $base)
            ->selectRaw(
                $isToday
                    ? "DATE_FORMAT(visited_at, '%H:00') as bucket, COUNT(*) as total"
                    : "DATE(visited_at) as bucket, COUNT(*) as total"
            )
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        // ── Recent visits ──
        $recent = (clone $base)
            ->latest('visited_at')
            ->limit(20)
            ->get();

        // ── By language ──
        $byLanguage = (clone $base)
            ->selectRaw('language, COUNT(*) as total')
            ->whereNotNull('language')
            ->groupBy('language')
            ->orderByDesc('total')
            ->get();

        return compact(
            'totalVisits', 'uniqueVisits', 'loggedIn',
            'byDevice', 'topPages', 'topBrowsers', 'topOS',
            'byTime', 'recent', 'byLanguage',
            'from', 'to',
        );
    }
}
