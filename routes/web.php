<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PageController;

// ── Language Switch ──
Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

// ── Maintenance Preview (admin only, bypasses middleware) ──
Route::get('/maintenance-preview', function () {
    $type    = \App\Models\Setting::get('maintenance_type', 'maintenance');
    $title   = \App\Models\Setting::get('maintenance_title', '');
    $message = \App\Models\Setting::get('maintenance_message', '');
    $launch  = \App\Models\Setting::get('maintenance_launch_date', '');
    $wa      = \App\Models\Setting::get('maintenance_whatsapp', '');
    return view('maintenance', compact('type','title','message','launch','wa'));
})->middleware('auth')->name('maintenance.preview');

// ── Admin Invoices & Export ──
Route::middleware('auth')->prefix('admin-tools')->group(function () {
    Route::get('invoice/{order}',  [InvoiceController::class, 'single'])->name('orders.invoice');
    Route::get('invoices/bulk',    [InvoiceController::class, 'bulk'])->name('orders.invoices.bulk');
    Route::get('invoices/export',  [InvoiceController::class, 'export'])->name('orders.export');
});

// ── Admin Notifications API (used by admin-notifications.js for popup toast) ──
Route::get('admin-api/latest-notification', function () {
    $user = auth()->user();
    if (! $user) return response()->json(['notification' => null]);
    $n = $user->unreadNotifications()->latest()->first();
    if (! $n) return response()->json(['notification' => null]);
    $d = $n->data ?? [];
    return response()->json([
        'notification' => [
            'id'    => $n->id,
            'title' => $d['title'] ?? '',
            'body'  => $d['body']  ?? '',
            'color' => $d['color'] ?? $d['icon_color'] ?? 'warning',
            'time'  => $n->created_at?->diffForHumans(),
            'url'   => $d['actions'][0]['url'] ?? null,
        ],
    ]);
})->middleware('auth')->name('admin.latest-notification');

// ── Store Pages ──
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::post('/products/{slug}/review', [ProductController::class, 'addReview'])->name('products.review')->middleware('auth');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// ── Cart ──
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{key}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{key}', [CartController::class, 'remove'])->name('remove');
    Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::post('/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

// ── Checkout ──
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/place', [CheckoutController::class, 'placeOrder'])->name('place');
    Route::get('/delivery-fee', [CheckoutController::class, 'getDeliveryFee'])->name('delivery-fee');
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('success');
    Route::get('/tracking/{orderNumber}', [CheckoutController::class, 'tracking'])->name('tracking');
});

// ── Account (Auth required) ──
Route::prefix('account')->name('account.')->middleware('auth')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
    Route::get('/points', [AccountController::class, 'points'])->name('points');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
});

// ── Static Pages ──
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/about', [PageController::class, 'about'])->name('about');

// ── Legal Pages ──
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/returns',        [PageController::class, 'returns'])->name('returns');
Route::get('/terms',          [PageController::class, 'terms'])->name('terms');
Route::get('/shipping',       [PageController::class, 'shipping'])->name('shipping');
Route::get('/faq',            [PageController::class, 'faq'])->name('faq');

// ── Wishlist ──
Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// ── Auth ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login'])
        ->middleware('throttle:5,1')      // 5 attempts per minute per IP
        ->name('login.submit');
    Route::get('/register', [AccountController::class, 'registerForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register'])
        ->middleware('throttle:3,10')     // 3 registrations per 10 minutes per IP
        ->name('register.submit');
});
Route::post('/logout', [AccountController::class, 'logout'])->name('logout')->middleware('auth');
