<?php

declare(strict_types = 1);

namespace Centrex\Cart\Exceptions;

use InvalidArgumentException;

class InvalidQtyException extends InvalidArgumentException
{
    public static function mustBePositive(): self
    {
        return new self('Quantity must be a positive integer (>= 1).');
    }
}
