<?php

declare(strict_types = 1);

namespace Centrex\Cart\Events;

class CartCleared
{
    public function __construct(
        public readonly string $instance,
    ) {}
}
