<?php

declare(strict_types = 1);

namespace Centrex\Cart;

use Centrex\Cart\Contracts\CartStorage;
use Centrex\Cart\Services\CartCheckoutService;
use Centrex\Cart\Storage\{CookieStorage, DatabaseStorage, SessionStorage};
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-cart');

        // Bind concrete storage drivers
        $this->app->bind(SessionStorage::class);
        $this->app->bind(CookieStorage::class);
        $this->app->bind(DatabaseStorage::class);

        // Resolve the configured storage driver
        $this->app->bind(CartStorage::class, function ($app): CartStorage {
            return match (config('laravel-cart.driver', 'session')) {
                'cookie'   => $app->make(CookieStorage::class),
                'database' => $app->make(DatabaseStorage::class),
                default    => $app->make(SessionStorage::class),
            };
        });

        // Singleton Cart service
        $this->app->singleton('laravel-cart', fn ($app): Cart => new Cart(
            $app->make(CartStorage::class),
            (string) config('laravel-cart.default_instance', 'default'),
        ));

        // Allow resolution by class name too
        $this->app->alias('laravel-cart', Cart::class);

        // Register checkout bridge only when laravel-inventory is installed
        if (class_exists(\Centrex\Inventory\Inventory::class)) {
            $this->app->singleton(CartCheckoutService::class, fn ($app): CartCheckoutService => new CartCheckoutService(
                $app->make(\Centrex\Inventory\Inventory::class),
                $app->make(Cart::class),
            ));
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-cart');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Register Livewire components if Livewire is installed
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('cart-icon', Livewire\CartIcon::class);
            \Livewire\Livewire::component('cart-drawer', Livewire\CartDrawer::class);
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-cart.php'),
            ], 'laravel-cart-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'laravel-cart-migrations');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-cart'),
            ], 'laravel-cart-views');
        }
    }
}
