<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\OrderAccess;
use Illuminate\Http\Request;

/**
 * Public "track my order" page for customers who never made an account.
 *
 * Requires the order number *and* the phone on the order. The order number
 * alone is sequential and therefore guessable, so it isn't proof of anything;
 * the phone is what ties the visitor to the order.
 */
class TrackOrderController extends Controller
{
    public function form()
    {
        return view('track.index');
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'order_number' => ['required', 'string', 'max:64'],
            'phone'        => ['required', 'string', 'max:32'],
        ]);

        $order = Order::where('order_number', trim($data['order_number']))->first();

        // One message for "no such order" and "wrong phone" alike — telling them
        // apart would turn this form into an order-number oracle.
        if (! $order || ! OrderAccess::phoneMatches($order, $data['phone'])) {
            return back()
                ->withErrors(['order_number' => __('track_not_found')])
                ->withInput();
        }

        OrderAccess::grant($order);

        return redirect()->route('checkout.tracking', $order->order_number);
    }
}
