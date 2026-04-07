<?php

declare(strict_types = 1);

use Centrex\Cart\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('laravel-cart.api_middleware', ['api']))
    ->prefix(config('laravel-cart.api_prefix', 'api'))
    ->group(function (): void {
        Route::get('cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('cart/items', [CartController::class, 'store'])->name('cart.items.store');
        Route::put('cart/items/{rowId}', [CartController::class, 'update'])->name('cart.items.update');
        Route::delete('cart/items/{rowId}', [CartController::class, 'destroy'])->name('cart.items.destroy');
        Route::delete('cart', [CartController::class, 'clear'])->name('cart.clear');
    });
