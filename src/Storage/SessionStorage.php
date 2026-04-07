<?php

declare(strict_types = 1);

namespace Centrex\Cart\Storage;

use Centrex\Cart\CartItem;
use Centrex\Cart\Contracts\CartStorage;
use Illuminate\Support\Collection;

class SessionStorage implements CartStorage
{
    /** @return Collection<string, CartItem> */
    public function get(string $key): Collection
    {
        return collect(session()->get($key, []));
    }

    /** @param Collection<string, CartItem> $items */
    public function put(string $key, Collection $items): void
    {
        session()->put($key, $items->all());
    }

    public function forget(string $key): void
    {
        session()->forget($key);
    }
}
