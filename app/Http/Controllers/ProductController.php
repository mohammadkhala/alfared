<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'brand']);

        // Filters
        if ($request->filled('category')) {
            $cats = (array) $request->category;
            $query->whereHas('category', fn($q) => $q->whereIn('slug', $cats));
        }
        if ($request->filled('brand')) {
            $brands = (array) $request->brand;
            $query->whereIn('brand_id', $brands);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('rating')) {
            $query->where('rating_avg', '>=', $request->rating);
        }
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('compare_price')->whereColumn('price', '<', 'compare_price');
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) => $sq
                ->where('name_ar', 'like', "%{$q}%")
                ->orWhere('name_en', 'like', "%{$q}%")
                ->orWhere('description_ar', 'like', "%{$q}%")
            );
        }

        // Sort
        match ($request->get('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating_avg', 'desc'),
            'bestseller' => $query->orderBy('sales_count', 'desc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(24)->withQueryString();
        $categories = Category::where('is_active', true)->whereNull('parent_id')->withCount('products')->get();
        $brands     = Brand::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show(string $slug)
    {
        $product = Product::active()->where('slug', $slug)
            ->with(['category', 'brand', 'images', 'variants', 'reviews.user'])
            ->withCount('reviews')
            ->firstOrFail();

        // Increment views
        $product->increment('views_count');

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['brand'])
            ->limit(6)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products = Product::active()
            ->where('category_id', $category->id)
            ->with(['brand'])
            ->latest()
            ->paginate(24);

        $categories = Category::where('is_active', true)->whereNull('parent_id')->withCount('products')->get();
        $brands     = Brand::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories', 'brands', 'category'));
    }

    public function search(Request $request)
    {
        $q        = $request->q;
        $products = Product::active()
            ->where(fn($query) => $query
                ->where('name_ar', 'like', "%{$q}%")
                ->orWhere('name_en', 'like', "%{$q}%")
                ->orWhere('name_he', 'like', "%{$q}%")
                ->orWhere('description_ar', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%")
            )
            ->with(['category', 'brand'])
            ->paginate(24)
            ->withQueryString();

        return view('products.search', compact('products', 'q'));
    }

    public function addReview(Request $request, string $slug)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $product = Product::where('slug', $slug)->firstOrFail();

        // Prevent duplicate review
        $exists = Review::where('product_id', $product->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'لقد قمت بتقييم هذا المنتج مسبقاً');
        }

        Review::create([
            'product_id'    => $product->id,
            'user_id'       => auth()->id(),
            'reviewer_name' => auth()->user()->name,
            'rating'        => $request->rating,
            'comment'       => $request->comment,
            'is_approved'   => true, // Auto-approve or set false for moderation
        ]);

        // Update product rating avg
        $avg = Review::where('product_id', $product->id)->where('is_approved', true)->avg('rating');
        $cnt = Review::where('product_id', $product->id)->where('is_approved', true)->count();
        $product->update(['rating_avg' => round($avg, 1), 'reviews_count' => $cnt]);

        return back()->with('success', 'شكراً! تم نشر تقييمك بنجاح ✓');
    }
}
