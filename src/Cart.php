<?php

declare(strict_types = 1);

namespace Centrex\Cart;

use Centrex\Cart\Contracts\CartStorage;
use Centrex\Cart\Events\{CartCleared, CartItemAdded, CartItemRemoved, CartItemUpdated};
use Centrex\Cart\Exceptions\{CartItemNotFoundException, InvalidQtyException};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class Cart
{
    private readonly string $instanceName;

    public function __construct(
        private readonly CartStorage $storage,
        string $instanceName = 'default',
    ) {
        $this->instanceName = $instanceName ?: (string) config('laravel-cart.default_instance', 'default');
    }

    /**
     * Return a new Cart bound to a different instance name (e.g. 'wishlist').
     * Does not mutate the current instance.
     */
    public function instance(string $name): static
    {
        return new static($this->storage, $name);
    }

    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    // ── CRUD ──────────────────────────────────────────────────────────────

    /**
     * Add an item to the cart.
     * If the same rowId already exists, the quantities are merged.
     */
    public function add(
        string|int $id,
        string $name,
        int $qty,
        float $price,
        array $options = [],
    ): CartItem {
        if ($qty < 1) {
            throw InvalidQtyException::mustBePositive();
        }

        $item = CartItem::make($id, $name, $qty, $price, $options);
        $content = $this->getContent();

        if ($content->has($item->rowId)) {
            $existing = $content->get($item->rowId);
            $item = $item->withQty($existing->qty + $qty);
        }

        $content->put($item->rowId, $item);
        $this->putContent($content);

        Event::dispatch(new CartItemAdded($item, $this->instanceName));

        return $item;
    }

    /**
     * Update the quantity of an existing cart item.
     *
     * @throws CartItemNotFoundException
     * @throws InvalidQtyException
     */
    public function update(string $rowId, int $qty): CartItem
    {
        if ($qty < 1) {
            throw InvalidQtyException::mustBePositive();
        }

        $content = $this->getContent();

        if (!$content->has($rowId)) {
            throw CartItemNotFoundException::forRowId($rowId);
        }

        $item = $content->get($rowId)->withQty($qty);
        $content->put($rowId, $item);
        $this->putContent($content);

        Event::dispatch(new CartItemUpdated($item, $this->instanceName));

        return $item;
    }

    /**
     * Remove an item from the cart.
     *
     * @throws CartItemNotFoundException
     */
    public function remove(string $rowId): void
    {
        $content = $this->getContent();

        if (!$content->has($rowId)) {
            throw CartItemNotFoundException::forRowId($rowId);
        }

        $content->forget($rowId);
        $this->putContent($content);

        Event::dispatch(new CartItemRemoved($rowId, $this->instanceName));
    }

    /** Remove all items from this cart instance. */
    public function clear(): void
    {
        $this->storage->forget($this->storageKey());

        Event::dispatch(new CartCleared($this->instanceName));
    }

    // ── Retrieval ─────────────────────────────────────────────────────────

    /**
     * Get a single cart item by rowId.
     *
     * @throws CartItemNotFoundException
     */
    public function get(string $rowId): CartItem
    {
        $item = $this->getContent()->get($rowId);

        if ($item === null) {
            throw CartItemNotFoundException::forRowId($rowId);
        }

        return $item;
    }

    /**
     * Get all items in this cart instance.
     *
     * @return Collection<string, CartItem>
     */
    public function content(): Collection
    {
        return $this->getContent();
    }

    // ── Aggregates ────────────────────────────────────────────────────────

    /** Total number of individual units across all items. */
    public function count(): int
    {
        return (int) $this->getContent()->sum('qty');
    }

    /** Number of distinct line items (ignoring quantities). */
    public function lines(): int
    {
        return $this->getContent()->count();
    }

    public function subtotal(): float
    {
        return round($this->getContent()->sum('subtotal'), 2);
    }

    public function tax(): float
    {
        $rate = (int) config('laravel-cart.tax', 0);

        return round($this->subtotal() * $rate / 100, 2);
    }

    public function total(): float
    {
        return round($this->subtotal() + $this->tax(), 2);
    }

    public function isEmpty(): bool
    {
        return $this->getContent()->isEmpty();
    }

    // ── Internal ──────────────────────────────────────────────────────────

    private function storageKey(): string
    {
        return 'cart.' . $this->instanceName;
    }

    /** @return Collection<string, CartItem> */
    private function getContent(): Collection
    {
        return $this->storage->get($this->storageKey());
    }

    /** @param Collection<string, CartItem> $items */
    private function putContent(Collection $items): void
    {
        $this->storage->put($this->storageKey(), $items);
    }
}
