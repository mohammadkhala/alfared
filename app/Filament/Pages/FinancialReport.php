<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class FinancialReport extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'التقارير المالية';
    protected static ?string $title           = 'التقارير المالية';
    protected static ?string $navigationGroup = 'التقارير';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.financial-report';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('view_financial_reports') ?? false;
    }

    public string $year;

    public function mount(): void
    {
        $this->year = (string) now()->year;
    }

    protected function getViewData(): array
    {
        $year = (int) $this->year;
        $arabicMonths = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                         'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

        // Year summary
        $yearRevenue   = (float) Order::whereYear('created_at', $year)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
        $yearOrders    = Order::whereYear('created_at', $year)->whereNotIn('status', ['cancelled', 'returned'])->count();
        $yearDelivery  = (float) Order::whereYear('created_at', $year)->whereNotIn('status', ['cancelled', 'returned'])->sum('delivery_fee');
        $yearDiscounts = (float) Order::whereYear('created_at', $year)->whereNotIn('status', ['cancelled', 'returned'])->sum('discount_amount');
        $yearCancelled = (float) Order::whereYear('created_at', $year)->where('status', 'cancelled')->sum('total');

        // Cost & gross profit (if products have cost_price)
        $yearCost = (float) DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders',   'order_items.order_id',   '=', 'orders.id')
            ->whereYear('orders.created_at', $year)
            ->whereNotIn('orders.status', ['cancelled', 'returned'])
            ->selectRaw('SUM(products.cost_price * order_items.quantity) as cost')
            ->value('cost') ?? 0;
        $grossProfit = $yearRevenue - $yearCost;
        $margin      = $yearRevenue > 0 ? round(($grossProfit / $yearRevenue) * 100, 1) : 0;

        // Monthly breakdown
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $rev  = (float) Order::whereYear('created_at', $year)->whereMonth('created_at', $m)->whereNotIn('status', ['cancelled', 'returned'])->sum('total');
            $cnt  = Order::whereYear('created_at', $year)->whereMonth('created_at', $m)->whereNotIn('status', ['cancelled', 'returned'])->count();
            $disc = (float) Order::whereYear('created_at', $year)->whereMonth('created_at', $m)->whereNotIn('status', ['cancelled', 'returned'])->sum('discount_amount');
            $del  = (float) Order::whereYear('created_at', $year)->whereMonth('created_at', $m)->whereNotIn('status', ['cancelled', 'returned'])->sum('delivery_fee');
            $monthly[] = [
                'month'    => $arabicMonths[$m],
                'num'      => $m,
                'revenue'  => $rev,
                'orders'   => $cnt,
                'discount' => $disc,
                'delivery' => $del,
                'avg'      => $cnt > 0 ? round($rev / $cnt, 2) : 0,
            ];
        }

        $maxMonthRev = max(array_column($monthly, 'revenue')) ?: 1;
        foreach ($monthly as &$row) {
            $row['pct'] = max(2, (int) round(($row['revenue'] / $maxMonthRev) * 100));
        }

        // Coupon usage
        $couponUsage = DB::table('orders')
            ->whereYear('created_at', $year)
            ->whereNotNull('coupon_code')
            ->where('coupon_code', '!=', '')
            ->select('coupon_code', DB::raw('COUNT(*) as uses'), DB::raw('SUM(discount_amount) as saved'))
            ->groupBy('coupon_code')
            ->orderByDesc('uses')
            ->limit(8)
            ->get();

        // Delivery fee vs free shipping
        $paidDelivery = Order::whereYear('created_at', $year)->where('delivery_fee', '>', 0)->count();
        $freeDelivery = Order::whereYear('created_at', $year)->where('delivery_fee', 0)->count();

        // Available years (for selector)
        $availableYears = Order::selectRaw('YEAR(created_at) as yr')->distinct()->orderByDesc('yr')->pluck('yr')->toArray();
        if (!in_array($year, $availableYears)) {
            $availableYears[] = $year;
        }
        rsort($availableYears);

        return compact(
            'year', 'yearRevenue', 'yearOrders', 'yearDelivery', 'yearDiscounts',
            'yearCancelled', 'yearCost', 'grossProfit', 'margin',
            'monthly', 'couponUsage', 'paidDelivery', 'freeDelivery', 'availableYears'
        );
    }
}
