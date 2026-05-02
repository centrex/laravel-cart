<?php

declare(strict_types = 1);

namespace Centrex\Cart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Centrex\Cart\Cart
 *
 * @method static \Centrex\Cart\Cart instance(string $name)
 * @method static \Centrex\Cart\CartItem add(string|int $id, string $name, int $qty, float $price, array $options = [])
 * @method static \Centrex\Cart\CartItem update(string $rowId, int $qty)
 * @method static void remove(string $rowId)
 * @method static void clear()
 * @method static \Centrex\Cart\CartItem get(string $rowId)
 * @method static \Illuminate\Support\Collection content()
 * @method static int count()
 * @method static int lines()
 * @method static float subtotal()
 * @method static float tax()
 * @method static float total()
 * @method static bool isEmpty()
 */
class Cart extends Facade
{
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-cart';
    }
}
