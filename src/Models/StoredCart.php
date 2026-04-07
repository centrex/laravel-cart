<?php

declare(strict_types = 1);

namespace Centrex\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class StoredCart extends Model
{
    protected $fillable = ['instance', 'identifier', 'content'];

    public function getTable(): string
    {
        return config('laravel-cart.database.table', 'carts');
    }

    public function getConnectionName(): ?string
    {
        return config('laravel-cart.database.connection') ?? config('database.default');
    }
}
