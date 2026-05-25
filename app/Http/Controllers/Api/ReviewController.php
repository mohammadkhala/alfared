<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** POST /api/v1/products/{slug}/reviews */
    public function store(Request $request, string $slug): JsonResponse
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $product = Product::where('slug', $slug)->firstOrFail();
        $user    = $request->user();

        // One review per user per product
        if (Review::where('product_id', $product->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'لقد قيّمت هذا المنتج من قبل'], 422);
        }

        // Verify the user actually bought this product (verified purchase)
        $verified = Order::where('user_id', $user->id)
            ->whereIn('status', ['delivered'])
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->exists();

        Review::create([
            'product_id'    => $product->id,
            'user_id'       => $user->id,
            'reviewer_name' => $user->name,
            'rating'        => $data['rating'],
            'comment'       => $data['comment'] ?? null,
            'is_approved'   => false, // pending admin approval
            'is_verified'   => $verified,
        ]);

        return response()->json(['message' => 'شكراً! سيظهر تقييمك بعد المراجعة.'], 201);
    }
}
