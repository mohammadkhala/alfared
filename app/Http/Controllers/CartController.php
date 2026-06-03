<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart     = session('cart', []);
        $coupon   = session('coupon');
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $discount = $coupon ? $coupon['discount'] : 0;
        $delivery = session('delivery_fee', 0);
        $total    = $subtotal - $discount + $delivery;

        return view('cart.index', compact('cart', 'coupon', 'subtotal', 'discount', 'delivery', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'qty' => 'integer|min:1']);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_in_stock) {
            return back()->with('error', 'المنتج غير متوفر حالياً');
        }

        $cart   = session('cart', []);
        $key    = $product->id . ($request->variant_id ? '_' . $request->variant_id : '');
        $qty    = $request->qty ?? 1;
        $price  = $product->price;

        // Apply variant price modifier
        if ($request->variant_id) {
            $variant = $product->variants()->find($request->variant_id);
            if ($variant) {
                $price += $variant->price_modifier;
            }
        }

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'variant_id' => $request->variant_id,
                'name'       => $product->name_ar,
                'image'      => $product->main_image,
                'price'      => $price,
                'qty'        => $qty,
                'variant'    => $request->variant_info ?? null,
            ];
        }

        session(['cart' => $cart]);

        if ($request->ajax()) {
            return response()->json([
                'success'   => true,
                'cartCount' => count($cart),
                'message'   => 'تمت الإضافة للسلة',
            ]);
        }

        if ($request->input('buy_now') == '1') {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'تمت إضافة المنتج للسلة ✓');
    }

    public function update(Request $request, string $key)
    {
        $cart = session('cart', []);
        if (isset($cart[$key])) {
            $cart[$key]['qty'] = max(1, (int)$request->qty);
            session(['cart' => $cart]);
        }
        return back();
    }

    public function remove(string $key)
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);
        return back()->with('success', 'تم حذف المنتج من السلة');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', 'كود الخصم غير صحيح أو منتهي الصلاحية');
        }

        $cart     = session('cart', []);
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);
        $discount = $coupon->calculateDiscount($subtotal);

        if ($discount <= 0) {
            return back()->with('error', "الحد الأدنى للطلب {$coupon->min_order_amount} ₪");
        }

        session(['coupon' => [
            'code'     => $coupon->code,
            'id'       => $coupon->id,
            'discount' => $discount,
            'label'    => $coupon->type === 'percentage' ? "{$coupon->value}%" : "{$coupon->value} ₪",
        ]]);

        return back()->with('success', "تم تطبيق الخصم: " . number_format($discount, 2) . " ₪ 🎉");
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'تم إزالة كود الخصم');
    }
}
