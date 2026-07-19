<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Puts an order's items back on the shelf when the sale doesn't happen.
 *
 * Stock is deducted the moment an order is created, so a cancelled order held
 * quantity that was never sold — over time the catalogue reads as out of stock
 * while the goods are sitting there.
 */
class StockService
{
    /**
     * Returns the items of a cancelled or returned order to stock.
     *
     * Guarded by orders.stock_restored_at: an admin flipping a status back and
     * forth, or a RoadFN sync landing on cancelled twice, must not keep adding
     * quantity that only left the shelf once.
     */
    public static function restoreForOrder(Order $order): bool
    {
        if ($order->stock_restored_at) {
            return false;
        }

        $order->loadMissing('items');

        if ($order->items->isEmpty()) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            // Re-check inside the transaction: two requests can reach this at
            // once (an admin clicking cancel while the sync applies the same
            // status), and both would otherwise restore.
            $fresh = Order::whereKey($order->id)->lockForUpdate()->first();

            if (! $fresh || $fresh->stock_restored_at) {
                return false;
            }

            foreach ($order->items as $item) {
                if (! $item->product_id) {
                    continue;
                }

                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

                // Mirror the deduction: checkout only takes stock from products
                // that track it, so only those get it back. Restoring more
                // broadly would invent quantity.
                if (! $product || ! $product->track_quantity) {
                    continue;
                }

                $product->increment('stock_quantity', (int) $item->quantity);
            }

            $fresh->forceFill(['stock_restored_at' => now()])->saveQuietly();
            $order->stock_restored_at = $fresh->stock_restored_at;

            Log::info('Stock restored for order', [
                'order'  => $order->order_number,
                'status' => $order->status,
                'items'  => $order->items->count(),
            ]);

            return true;
        });
    }
}
