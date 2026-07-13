<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\HotDealController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'productReviews']);

// Store Routes (Public)
Route::prefix('store')->group(function () {
    Route::get('/products', [StoreController::class, 'products']);
    Route::get('/categories', [StoreController::class, 'categories']);
    Route::get('/brands', [StoreController::class, 'brands']);
    Route::get('/products/{id}', [StoreController::class, 'product']);
    Route::get('/hot-deals', function () {
        return \App\Models\HotDeal::with('product')
            ->visibleForStorefront()
            ->latest()
            ->get();
    });
});

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Customer Routes (Own orders)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store']);

    Route::post('/checkout/create-payment-intent', [CheckoutController::class, 'createPaymentIntent']);

    // Product Reviews (Create)
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);

    // My Orders
    Route::get('/my-orders', [OrderController::class, 'myOrders']);
    Route::get('/my-orders/{order}', [OrderController::class, 'myOrderShow']);
    Route::delete('/my-orders/{order}', [OrderController::class, 'destroyMyOrder']);
    Route::put('/my-orders/{order}/cancel', [OrderController::class, 'cancel']);

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // wishlist

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    // Coupon Routes (Apply for customers)
    Route::post('/coupons/apply', [CouponController::class, 'apply']);

    // invoice Routes
    Route::get(
        '/invoice/{order}',
        [InvoiceController::class, 'show']
    );
});

// Admin Routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Category Routes
    Route::apiResource('categories', CategoryController::class);

    // Brand Routes
    Route::apiResource('brands', BrandController::class);

    // Product Routes
    Route::apiResource('products', ProductController::class);

    // Order Management
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    Route::patch('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Customer Routes
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{user}', [CustomerController::class, 'show']);

    // Report Routes
    Route::get('/reports/sales', [ReportController::class, 'sales']);
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf']);

    // Review Routes (Admin)
    Route::apiResource('reviews', ReviewController::class);
    

    // Contact Routes (Admin)
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/contacts/unread-count', [ContactController::class, 'unreadCount']);
    Route::get('/contacts/{contact}', [ContactController::class, 'show']);
    Route::patch('/contacts/{contact}/read', [ContactController::class, 'markAsRead']);
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);

    Route::apiResource('coupons', CouponController::class);

    // Hot Deal Routes
    Route::apiResource('hot-deals', HotDealController::class);
});
