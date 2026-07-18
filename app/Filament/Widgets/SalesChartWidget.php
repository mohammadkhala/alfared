<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class SalesChartWidget extends Widget
{
    protected static string $view = 'filament.widgets.sales-chart-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;

    protected function getViewData(): array
    {
        $arabicDays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

        $daily = [];
        for ($i = 6; $i >= 0; $i--) {
            $date    = now()->subDays($i);
            $revenue = (float) Order::whereDate('created_at', $date)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
            $daily[] = ['label' => $arabicDays[$date->dayOfWeek], 'value' => $revenue];
        }

        $maxVal = max(array_column($daily, 'value')) ?: 1;
        foreach ($daily as &$d) {
            $d['pct'] = max(4, (int) round(($d['value'] / $maxVal) * 100));
            $d['display'] = $d['value'] >= 1000 ? round($d['value'] / 1000, 1) . 'k' : (int) $d['value'];
        }

        // Category breakdown via order items
        $categoryColors = ['#E8711A', '#1B3B8C', '#10B981', '#8B5CF6', '#94A3B8'];
        $rows = DB::table('order_items')
            ->join('products',   'order_items.product_id',   '=', 'products.id')
            ->join('categories', 'products.category_id',     '=', 'categories.id')
            ->join('orders',     'order_items.order_id',     '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->select('categories.name_ar', DB::raw('SUM(order_items.total) as total'))
            ->groupBy('categories.id', 'categories.name_ar')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $totalSales = $rows->sum('total') ?: 1;
        $categories = [];
        $pos        = 0;
        foreach ($rows as $i => $row) {
            $pct          = (int) round(($row->total / $totalSales) * 100);
            $color        = $categoryColors[$i] ?? '#94A3B8';
            $categories[] = ['name' => $row->name_ar, 'pct' => $pct, 'color' => $color, 'from' => $pos, 'to' => $pos + $pct];
            $pos         += $pct;
        }

        // Fill remainder to 100 for conic gradient
        if ($pos < 100 && count($categories)) {
            $categories[count($categories) - 1]['to'] = 100;
        }
        $donutGradient = count($categories)
            ? implode(', ', array_map(fn($c) => "{$c['color']} {$c['from']}% {$c['to']}%", $categories))
            : '#94A3B8 0% 100%';

        return compact('daily', 'categories', 'donutGradient');
    }
}
