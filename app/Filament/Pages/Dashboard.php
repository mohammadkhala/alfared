<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    protected function getViewData(): array
    {
        // ── KPI ──
        $monthRevenue     = (float) Order::whereMonth('created_at', now()->month)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
        $prevRevenue      = (float) Order::whereMonth('created_at', now()->subMonth()->month)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
        $revenueChange    = $prevRevenue > 0 ? round((($monthRevenue - $prevRevenue) / $prevRevenue) * 100) : 0;

        $todayOrders      = Order::whereDate('created_at', today())->count();
        $yesterdayOrders  = Order::whereDate('created_at', today()->subDay())->count();
        $ordersChange     = $yesterdayOrders > 0 ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100) : 0;

        $weekCustomers    = User::whereBetween('created_at', [now()->startOfWeek(), now()])->count();
        $prevCustomers    = User::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $customersChange  = $prevCustomers > 0 ? round((($weekCustomers - $prevCustomers) / $prevCustomers) * 100) : 0;

        $lowStock         = Product::whereColumn('stock_quantity', '<=', 'low_stock_alert')->where('stock_quantity', '>', 0)->count();

        // ── DAILY SALES CHART (last 7 days) ──
        $arabicDays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        $daily = [];
        for ($i = 6; $i >= 0; $i--) {
            $date    = now()->subDays($i);
            $revenue = (float) Order::whereDate('created_at', $date)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
            $daily[] = ['label' => $arabicDays[$date->dayOfWeek], 'value' => $revenue];
        }
        $maxVal = max(array_column($daily, 'value')) ?: 1;
        foreach ($daily as &$d) {
            $d['pct']     = max(4, (int) round(($d['value'] / $maxVal) * 100));
            $d['display'] = $d['value'] >= 1000 ? round($d['value'] / 1000, 1) . 'k' : (int) $d['value'];
        }

        // ── CATEGORY DONUT ──
        $catColors = ['#E8711A', '#1B3B8C', '#10B981', '#8B5CF6', '#94A3B8'];
        $catRows = DB::table('order_items')
            ->join('products',   'order_items.product_id',   '=', 'products.id')
            ->join('categories', 'products.category_id',     '=', 'categories.id')
            ->join('orders',     'order_items.order_id',     '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->select('categories.name_ar', DB::raw('SUM(order_items.total) as total'))
            ->groupBy('categories.id', 'categories.name_ar')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        $totalCat = $catRows->sum('total') ?: 1;
        $categories = [];
        $pos = 0;
        foreach ($catRows as $i => $row) {
            $pct          = (int) round(($row->total / $totalCat) * 100);
            $categories[] = ['name' => $row->name_ar, 'pct' => $pct, 'color' => $catColors[$i] ?? '#94A3B8', 'from' => $pos, 'to' => $pos + $pct];
            $pos         += $pct;
        }
        if (count($categories) && $pos < 100) {
            $categories[count($categories) - 1]['to'] = 100;
        }
        $donutGradient = count($categories)
            ? implode(', ', array_map(fn($c) => "{$c['color']} {$c['from']}% {$c['to']}%", $categories))
            : '#E5E7EB 0% 100%';

        // ── RECENT ORDERS ──
        $recentOrders = Order::latest()->limit(5)->get();

        // ── TOP PRODUCTS ──
        $topProducts = Product::where('is_active', true)->orderByDesc('sales_count')->limit(5)->get();

        return compact(
            'monthRevenue', 'revenueChange',
            'todayOrders', 'ordersChange',
            'weekCustomers', 'customersChange',
            'lowStock', 'daily', 'categories', 'donutGradient',
            'recentOrders', 'topProducts'
        );
    }
}
