<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/store', [ProductController::class, 'index'])->name('store');
Route::get('/cart', [CartController::class, 'index'])->name('cart');

// Cart API Routes
Route::prefix('cart')->group(function () {
    Route::get('/data', [CartController::class, 'getCartData'])->name('cart.data');
    Route::post('/add/{productId}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// Coupon API Routes
Route::prefix('coupon')->group(function () {
    Route::post('/apply', [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/remove', [CouponController::class, 'remove'])->name('coupon.remove');
    Route::post('/validate', [CouponController::class, 'validate'])->name('coupon.validate');
});

Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// API Routes for AJAX calls
Route::prefix('api')->group(function () {
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::get('/products/{product}', [ProductController::class, 'apiShow']);
    Route::get('/products/featured', [ProductController::class, 'apiFeatured']);
    Route::get('/products/categories/{category}', [ProductController::class, 'apiByCategory']);
    Route::get('/categories', function() {
        $categories = \App\Models\Category::active()->orderBy('sort_order')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    });
    Route::get('/categories/all', function() {
        $categories = \App\Models\Category::active()->orderBy('sort_order')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    });
    Route::post('/contact', [HomeController::class, 'contact']);
    Route::post('/order-confirmation', [HomeController::class, 'orderConfirmation']);
});

// Order Routes
Route::prefix('order')->group(function () {
    Route::post('/checkout', [OrderController::class, 'processCheckout'])->name('order.checkout');
    Route::get('/success/{order}', [OrderController::class, 'orderSuccess'])->name('order.success');
    Route::get('/failed/{order}', [OrderController::class, 'orderFailed'])->name('order.failed');
    Route::get('/pending/{order}', [OrderController::class, 'orderPending'])->name('order.pending');
    Route::get('/status/{order}', [OrderController::class, 'checkPaymentStatus'])->name('order.status');
});

// PhonePe Payment Gateway Routes
Route::prefix('phonepe')->group(function () {
    Route::post('/callback', [OrderController::class, 'phonePeCallback'])->name('phonepe.callback');
    Route::any('/redirect', [OrderController::class, 'phonePeRedirect'])->name('phonepe.redirect');
});

Route::get('/user', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes to serve static HTML files (for backward compatibility)
Route::get('/static', function () {
    return response()->file(public_path('index.html'));
})->name('static.home');

Route::get('/static/store', function () {
    return response()->file(public_path('store.html'));
})->name('static.store');

Route::get('/static/cart', function () {
    return response()->file(public_path('cart.html'));
})->name('static.cart'); 