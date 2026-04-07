<?php

declare(strict_types = 1);

namespace Centrex\Cart\Storage;

use Centrex\Cart\CartItem;
use Centrex\Cart\Contracts\CartStorage;
use Centrex\Cart\Models\StoredCart;
use Illuminate\Support\Collection;

class DatabaseStorage implements CartStorage
{
    /** @return Collection<string, CartItem> */
    public function get(string $key): Collection
    {
        $record = StoredCart::where('instance', $key)
            ->where('identifier', $this->identifier())
            ->first();

        if ($record === null) {
            return collect();
        }

        try {
            $items = unserialize($record->content);

            return collect(is_array($items) ? $items : []);
        } catch (\Throwable) {
            return collect();
        }
    }

    /** @param Collection<string, CartItem> $items */
    public function put(string $key, Collection $items): void
    {
        StoredCart::updateOrCreate(
            [
                'instance'   => $key,
                'identifier' => $this->identifier(),
            ],
            [
                'content'    => serialize($items->all()),
                'updated_at' => now(),
            ],
        );
    }

    public function forget(string $key): void
    {
        StoredCart::where('instance', $key)
            ->where('identifier', $this->identifier())
            ->delete();
    }

    /** Session ID for guests, user ID (as string) for authenticated users. */
    private function identifier(): string
    {
        if (auth()->check()) {
            return (string) auth()->id();
        }

        return session()->getId();
    }
}
