<?php

declare(strict_types = 1);

namespace Centrex\Cart\Livewire;

use Centrex\Cart\Facades\Cart;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartIcon extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->count = Cart::count();
    }

    #[On('cart-updated')]
    public function refreshCount(): void
    {
        $this->count = Cart::count();
    }

    public function render(): View
    {
        return view('laravel-cart::livewire.cart-icon');
    }
}
