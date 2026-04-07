<?php

declare(strict_types = 1);

namespace Centrex\Cart\Storage;

use Centrex\Cart\CartItem;
use Centrex\Cart\Contracts\CartStorage;
use Illuminate\Contracts\Cookie\Factory as CookieFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

class CookieStorage implements CartStorage
{
    public function __construct(
        private readonly Request $request,
    ) {}

    /** @return Collection<string, CartItem> */
    public function get(string $key): Collection
    {
        $cookieName = $this->cookieName($key);
        $raw        = $this->request->cookie($cookieName);

        if ($raw === null) {
            return collect();
        }

        try {
            $decoded = base64_decode($raw, strict: true);

            if ($decoded === false) {
                return collect();
            }

            $items = unserialize($decoded);

            return collect(is_array($items) ? $items : []);
        } catch (\Throwable) {
            return collect();
        }
    }

    /** @param Collection<string, CartItem> $items */
    public function put(string $key, Collection $items): void
    {
        $cookieName = $this->cookieName($key);
        $lifetime   = (int) config('laravel-cart.cookie_lifetime', 43200);
        $encoded    = base64_encode(serialize($items->all()));

        Cookie::queue(Cookie::make($cookieName, $encoded, $lifetime));
    }

    public function forget(string $key): void
    {
        Cookie::queue(Cookie::forget($this->cookieName($key)));
    }

    private function cookieName(string $key): string
    {
        $prefix = config('laravel-cart.cookie_name_prefix', 'laravel_cart_');

        return $prefix . str_replace('.', '_', $key);
    }
}
