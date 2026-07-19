<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Alfared Mobile App API — v1
|--------------------------------------------------------------------------
| Base URL: /api/v1
| Auth:     Sanctum bearer tokens (Authorization: Bearer <token>)
*/

Route::prefix('v1')->group(function () {

    // ── Public endpoints ───────────────────────────────────
    Route::post('auth/register',           [AuthController::class, 'register'])->middleware('throttle:5,10');
    Route::post('auth/login',              [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('auth/check-availability', [AuthController::class, 'checkAvailability'])->middleware('throttle:10,1');
    Route::post('auth/send-otp',           [AuthController::class, 'sendOtp'])->middleware('throttle:3,1');
    Route::post('auth/verify-otp',         [AuthController::class, 'verifyOtp'])->middleware('throttle:5,1');
    Route::post('auth/send-email-verification', [AuthController::class, 'sendEmailVerification'])->middleware('throttle:5,10');
    Route::post('auth/forgot-password',    [AuthController::class, 'forgotPassword'])->middleware('throttle:5,10');
    Route::post('auth/reset-password',     [AuthController::class, 'resetPassword'])->middleware('throttle:10,10');

    Route::get('app-config',      [CatalogController::class, 'appConfig']);
    Route::get('home',            [CatalogController::class, 'home']);
    Route::get('categories',      [CatalogController::class, 'categories']);
    Route::get('banners',         [CatalogController::class, 'banners']);
    Route::get('offers',          [CatalogController::class, 'offers']);
    Route::get('products',        [CatalogController::class, 'products']);
    Route::get('products/{slug}', [CatalogController::class, 'show']);

    Route::get ('delivery-zones',                         [CartController::class,  'deliveryZones']);
    Route::get ('delivery-zones/areas',                   [CartController::class,  'zoneAreas']);
    Route::get ('orders/{orderNumber}/track',             [OrderController::class, 'track'])->middleware('throttle:20,1');
    Route::post('orders/{orderNumber}/verify-payment',    [OrderController::class, 'verifyPayment'])->middleware('throttle:10,1');

    // ── Authenticated endpoints ────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
        // Auth + profile
        Route::post  ('auth/logout',          [AuthController::class, 'logout']);
        Route::get   ('auth/me',              [AuthController::class, 'me']);
        Route::put   ('auth/profile',         [AuthController::class, 'updateProfile']);
        Route::put   ('auth/fcm-token',       [AuthController::class, 'updateFcmToken']);
        Route::delete('auth/account',         [AuthController::class, 'deleteAccount']);

        // Cart
        Route::get   ('cart',                  [CartController::class, 'index']);
        Route::post  ('cart/add',              [CartController::class, 'add']);
        Route::put   ('cart/{key}',            [CartController::class, 'update']);
        Route::delete('cart/{key}',            [CartController::class, 'remove']);
        Route::delete('cart',                  [CartController::class, 'clear']);
        Route::post  ('cart/coupon',           [CartController::class, 'applyCoupon'])->middleware('throttle:10,1');
        Route::post  ('cart/preview',          [CartController::class, 'preview']);

        // Checkout
        Route::post('checkout',                [CartController::class, 'checkout']);

        // Orders
        Route::get ('orders',                              [OrderController::class, 'index']);
        Route::get ('orders/{orderNumber}',                [OrderController::class, 'show']);
        Route::post('orders/{orderNumber}/cancel',         [OrderController::class, 'cancel']);
        Route::post('orders/{orderNumber}/return-request', [OrderController::class, 'returnRequest']);

        // Reviews
        Route::post('products/{slug}/reviews',             [ReviewController::class, 'store']);

        // Wishlist
        Route::get ('wishlist',                [OrderController::class, 'wishlist']);
        Route::post('wishlist/toggle',         [OrderController::class, 'wishlistToggle']);

        // Loyalty
        Route::get('loyalty',                  [OrderController::class, 'loyalty']);

        // Addresses
        Route::get   ('addresses',             [UserProfileController::class, 'addresses']);
        Route::post  ('addresses',             [UserProfileController::class, 'addAddress']);
        Route::put   ('addresses/{id}',        [UserProfileController::class, 'updateAddress']);
        Route::delete('addresses/{id}',        [UserProfileController::class, 'deleteAddress']);

        // Daily check-in
        Route::get('checkin',                  [UserProfileController::class, 'checkinStatus']);
        Route::post('checkin',                 [UserProfileController::class, 'checkin']);

        // Referral
        Route::get('referral',                 [UserProfileController::class, 'referralInfo']);
    });
});

// Health check
Route::get('/up', fn () => response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]));
