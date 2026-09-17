<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'index'])->name('store.index');
Route::post('/wishlist/{product}', [StoreController::class, 'toggleWishlist'])->name('wishlist.toggle');

Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{lineKey}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{lineKey}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/verify', [PaymentController::class, 'verify'])->name('checkout.verify');

Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
