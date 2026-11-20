<?php

use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/shop', [ShopController::class, 'shop'])->name('shop.index');
Route::get('/about', fn () => view('pages.about'))->name('about');
Route::get('/contact', fn () => view('pages.contact'))->name('contact');
Route::post('/contact', [ShopController::class, 'contact'])->middleware('throttle:10,1')->name('contact.send');
Route::get('/categories/{category}', [ShopController::class, 'category'])->name('categories.show');
Route::get('/products/{slug}', [ShopController::class, 'product'])->name('products.show');
Route::get('/cart', [ShopController::class, 'cart'])->name('cart.index');
Route::post('/cart/{product}', [ShopController::class, 'addToCart'])->whereNumber('product')->name('cart.add');
Route::patch('/cart/{product}', [ShopController::class, 'updateCart'])->whereNumber('product')->name('cart.update');
Route::delete('/cart/{product}', [ShopController::class, 'removeFromCart'])->whereNumber('product')->name('cart.remove');
Route::post('/checkout/whatsapp', [ShopController::class, 'checkout'])->middleware('throttle:10,1')->name('checkout.whatsapp');
