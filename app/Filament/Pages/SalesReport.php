<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SalesReport extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'تقارير المبيعات';
    protected static ?string $title           = 'تقارير المبيعات';
    protected static ?string $navigationGroup = 'التقارير';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.sales-report';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('view_sales_reports') ?? false;
    }

    public string $period = 'month';

    protected function getViewData(): array
    {
        $period = $this->period;

        // Date range based on selected period
        [$from, $to, $prevFrom, $prevTo, $periodLabel] = match($period) {
            'today' => [today(), now(), today()->subDay(), today()->subDay()->endOfDay(), 'اليوم'],
            'week'  => [now()->startOfWeek(), now(), now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek(), 'هذا الأسبوع'],
            'year'  => [now()->startOfYear(), now(), now()->subYear()->startOfYear(), now()->subYear()->endOfYear(), 'هذا العام'],
            default => [now()->startOfMonth(), now(), now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(), 'هذا الشهر'],
        };

        $base = Order::whereNotIn('status', ['cancelled', 'returned']);

        // Current period stats
        $revenue      = (float) (clone $base)->whereBetween('created_at', [$from, $to])->sum('total');
        $ordersCount  = (clone $base)->whereBetween('created_at', [$from, $to])->count();
        $avgOrder     = $ordersCount > 0 ? round($revenue / $ordersCount, 2) : 0;
        $itemsSold    = (int) DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->whereBetween('orders.created_at', [$from, $to])
            ->sum('order_items.quantity');

        // Previous period for comparison
        $prevRevenue  = (float) (clone $base)->whereBetween('created_at', [$prevFrom, $prevTo])->sum('total');
        $revenueChange = $prevRevenue > 0 ? round((($revenue - $prevRevenue) / $prevRevenue) * 100) : 0;

        // Order status breakdown
        $statusBreakdown = Order::whereBetween('created_at', [$from, $to])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();

        // Daily sales for chart (last 30 days for month, 12 months for year, 7 days for week)
        $chartData = match($period) {
            'week' => $this->getDailyChart(7),
            'year' => $this->getMonthlyChart(),
            'today'=> $this->getHourlyChart(),
            default=> $this->getDailyChart(30),
        };

        // Top products by revenue
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders',   'order_items.order_id',   '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->whereBetween('orders.created_at', [$from, $to])
            ->select(
                'products.name_ar',
                'products.main_image',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(order_items.total) as revenue')
            )
            ->groupBy('products.id', 'products.name_ar', 'products.main_image')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Sales by category
        $byCategory = DB::table('order_items')
            ->join('products',   'order_items.product_id',   '=', 'products.id')
            ->join('categories', 'products.category_id',     '=', 'categories.id')
            ->join('orders',     'order_items.order_id',     '=', 'orders.id')
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->whereBetween('orders.created_at', [$from, $to])
            ->select('categories.name_ar', DB::raw('SUM(order_items.total) as total'), DB::raw('COUNT(DISTINCT orders.id) as orders'))
            ->groupBy('categories.id', 'categories.name_ar')
            ->orderByDesc('total')
            ->get();

        // Top cities
        $topCities = Order::whereBetween('created_at', [$from, $to])
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->select('city', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('city')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        return compact(
            'period', 'periodLabel', 'from', 'to',
            'revenue', 'ordersCount', 'avgOrder', 'itemsSold',
            'revenueChange', 'prevRevenue',
            'statusBreakdown', 'chartData',
            'topProducts', 'byCategory', 'topCities'
        );
    }

    private function getDailyChart(int $days): array
    {
        $arabicDays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date    = now()->subDays($i);
            $revenue = (float) Order::whereDate('created_at', $date)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
            $result[] = [
                'label' => $days <= 7 ? $arabicDays[$date->dayOfWeek] : $date->format('d/m'),
                'value' => $revenue,
            ];
        }
        $max = max(array_column($result, 'value')) ?: 1;
        foreach ($result as &$r) {
            $r['pct']     = max(2, (int) round(($r['value'] / $max) * 100));
            $r['display'] = $r['value'] >= 1000 ? round($r['value'] / 1000, 1) . 'k' : (int) $r['value'];
        }
        return $result;
    }

    private function getMonthlyChart(): array
    {
        $arabicMonths = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenue = (float) Order::whereYear('created_at', now()->year)->whereMonth('created_at', $m)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
            $result[] = ['label' => $arabicMonths[$m], 'value' => $revenue];
        }
        $max = max(array_column($result, 'value')) ?: 1;
        foreach ($result as &$r) {
            $r['pct']     = max(2, (int) round(($r['value'] / $max) * 100));
            $r['display'] = $r['value'] >= 1000 ? round($r['value'] / 1000, 1) . 'k' : (int) $r['value'];
        }
        return $result;
    }

    private function getHourlyChart(): array
    {
        $result = [];
        for ($h = 0; $h <= 23; $h++) {
            $revenue = (float) Order::whereDate('created_at', today())
                ->whereRaw('HOUR(created_at) = ?', [$h])
                ->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
            $result[] = ['label' => $h . ':00', 'value' => $revenue];
        }
        $max = max(array_column($result, 'value')) ?: 1;
        foreach ($result as &$r) {
            $r['pct']     = max(2, (int) round(($r['value'] / $max) * 100));
            $r['display'] = $r['value'] > 0 ? (int) $r['value'] : '';
        }
        return $result;
    }
}
