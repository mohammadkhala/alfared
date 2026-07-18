<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\Widget;

class KpiWidget extends Widget
{
    protected static string $view = 'filament.widgets.kpi-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        $monthRevenue     = Order::whereMonth('created_at', now()->month)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
        $prevMonthRevenue = Order::whereMonth('created_at', now()->subMonth()->month)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
        $monthChange      = $prevMonthRevenue > 0 ? round((($monthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100) : 0;

        $todayOrders      = Order::whereDate('created_at', today())->count();
        $yesterdayOrders  = Order::whereDate('created_at', today()->subDay())->count();
        $ordersChange     = $yesterdayOrders > 0 ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100) : 0;

        $weekCustomers    = User::whereBetween('created_at', [now()->startOfWeek(), now()])->count();
        $prevCustomers    = User::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $customersChange  = $prevCustomers > 0 ? round((($weekCustomers - $prevCustomers) / $prevCustomers) * 100) : 0;

        $lowStock = Product::whereColumn('stock_quantity', '<=', 'low_stock_alert')->where('stock_quantity', '>', 0)->count();

        return compact('monthRevenue', 'monthChange', 'todayOrders', 'ordersChange', 'weekCustomers', 'customersChange', 'lowStock');
    }
}
