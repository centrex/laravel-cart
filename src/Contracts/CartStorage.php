<?php

declare(strict_types = 1);

namespace Centrex\Cart\Contracts;

use Centrex\Cart\CartItem;
use Illuminate\Support\Collection;

interface CartStorage
{
    /**
     * Retrieve stored cart items for the given key.
     *
     * @return Collection<string, CartItem>
     */
    public function get(string $key): Collection;

    /**
     * Persist cart items under the given key.
     *
     * @param Collection<string, CartItem> $items
     */
    public function put(string $key, Collection $items): void;

    /** Remove all items stored under the given key. */
    public function forget(string $key): void;
}
