<?php

namespace App\Filament\Widgets;

use App\Models\SiteVisit;
use Filament\Widgets\Widget;

class VisitorsWidget extends Widget
{
    protected static string $view = 'filament.widgets.visitors-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return auth()->user()?->hasPermission('view_sales_reports')
            ?? (auth()->user()?->role === 'admin');
    }

    protected function getViewData(): array
    {
        $todayBase = SiteVisit::whereDate('visited_at', today());

        $today = (clone $todayBase)->count();
        $todayUnique = (clone $todayBase)->distinct('visitor_id')->count('visitor_id');

        $byDevice = (clone $todayBase)
            ->selectRaw('device_type, COUNT(*) as total')
            ->groupBy('device_type')
            ->pluck('total', 'device_type')
            ->toArray();

        return compact('today', 'todayUnique', 'byDevice');
    }
}
