<?php

declare(strict_types = 1);

namespace Centrex\Cart\Events;

class CartItemRemoved
{
    public function __construct(
        public readonly string $rowId,
        public readonly string $instance,
    ) {}
}
