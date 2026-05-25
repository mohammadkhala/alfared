<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class InventoryPage extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'المخزون';
    protected static ?string $title           = 'إدارة المخزون';
    protected static ?string $navigationGroup = 'المخزون';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.inventory';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('manage_inventory') ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Product::where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    protected function getViewData(): array
    {
        $total     = Product::count();
        $inStock   = Product::where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '>', 'low_stock_alert')
            ->count();
        $lowStock  = Product::where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->count();
        $outOfStock = Product::where('stock_quantity', 0)->count();

        $products = Product::with('category')
            ->orderBy('stock_quantity', 'asc')
            ->get()
            ->map(function ($p) {
                $max = max($p->stock_quantity, $p->low_stock_alert * 3, 1);
                return [
                    'id'          => $p->id,
                    'name'        => $p->name_ar,
                    'sku'         => $p->sku,
                    'category'    => $p->category?->name_ar ?? '—',
                    'image'       => $p->main_image,
                    'qty'         => $p->stock_quantity,
                    'low_alert'   => $p->low_stock_alert,
                    'price'       => $p->price,
                    'bar_pct'     => min(100, (int) round(($p->stock_quantity / $max) * 100)),
                    'status'      => match(true) {
                        $p->stock_quantity === 0       => 'out',
                        $p->stock_quantity <= $p->low_stock_alert => 'low',
                        default                        => 'ok',
                    },
                ];
            });

        return compact('total', 'inStock', 'lowStock', 'outOfStock', 'products');
    }
}
