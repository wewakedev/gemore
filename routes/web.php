<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

// Test route to check if our routes are loading
Route::get('/test', function () {
    return 'Routes are working!';
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/store', [ProductController::class, 'index'])->name('store');
Route::get('/cart', [CartController::class, 'index'])->name('cart');

Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// API Routes for AJAX calls
Route::prefix('api')->group(function () {
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::post('/contact', [HomeController::class, 'contact']);
    Route::post('/order-confirmation', [HomeController::class, 'orderConfirmation']);
});

Route::get('/user', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); 