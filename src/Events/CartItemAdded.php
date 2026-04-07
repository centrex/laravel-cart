<?php

declare(strict_types = 1);

namespace Centrex\Cart\Events;

use Centrex\Cart\CartItem;

class CartItemAdded
{
    public function __construct(
        public readonly CartItem $item,
        public readonly string $instance,
    ) {}
}
