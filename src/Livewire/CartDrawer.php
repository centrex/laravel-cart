<?php

declare(strict_types = 1);

namespace Centrex\Cart\Livewire;

use Centrex\Cart\CartItem;
use Centrex\Cart\Exceptions\CartItemNotFoundException;
use Centrex\Cart\Facades\Cart;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $open = false;

    /** @var Collection<string, CartItem> */
    public Collection $items;

    public float $subtotal = 0.0;

    public float $tax = 0.0;

    public float $total = 0.0;

    public ?string $flash = null;

    public function mount(): void
    {
        $this->items = collect();
        $this->loadCart();
    }

    #[On('cart-updated')]
    public function refresh(): void
    {
        $this->loadCart();
    }

    #[On('open-cart')]
    public function openDrawer(): void
    {
        $this->open = true;
    }

    public function closeDrawer(): void
    {
        $this->open = false;
    }

    public function updateQty(string $rowId, int $qty): void
    {
        if ($qty < 1) {
            $this->removeItem($rowId);

            return;
        }

        try {
            Cart::update($rowId, $qty);
            $this->dispatchCartUpdated();
        } catch (CartItemNotFoundException) {
            $this->flash = 'Item not found.';
        }
    }

    public function removeItem(string $rowId): void
    {
        try {
            Cart::remove($rowId);
            $this->dispatchCartUpdated();
        } catch (CartItemNotFoundException) {
            $this->flash = 'Item not found.';
        }
    }

    public function clearCart(): void
    {
        Cart::clear();
        $this->dispatchCartUpdated();
    }

    public function render(): View
    {
        return view('laravel-cart::livewire.cart-drawer');
    }

    private function loadCart(): void
    {
        $this->items = Cart::content();
        $this->subtotal = Cart::subtotal();
        $this->tax = Cart::tax();
        $this->total = Cart::total();
    }

    private function dispatchCartUpdated(): void
    {
        $this->loadCart();
        $this->dispatch('cart-updated');
    }
}
