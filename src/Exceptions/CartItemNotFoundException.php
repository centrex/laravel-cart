<?php

declare(strict_types = 1);

namespace Centrex\Cart\Exceptions;

use RuntimeException;

class CartItemNotFoundException extends RuntimeException
{
    public static function forRowId(string $rowId): self
    {
        return new self("Cart item with row ID [{$rowId}] not found.");
    }
}
