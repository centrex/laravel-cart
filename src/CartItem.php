<?php

declare(strict_types = 1);

namespace Centrex\Cart;

class CartItem
{
    public readonly string $rowId;

    public readonly float $subtotal;

    public function __construct(
        public readonly string|int $id,
        public readonly string $name,
        public readonly int $qty,
        public readonly float $price,
        public readonly array $options = [],
    ) {
        $this->rowId = self::generateRowId($id, $options);
        $this->subtotal = round($qty * $price, 2);
    }

    public static function make(
        string|int $id,
        string $name,
        int $qty,
        float $price,
        array $options = [],
    ): self {
        return new self($id, $name, $qty, $price, $options);
    }

    /** Returns a new CartItem with an updated quantity. */
    public function withQty(int $qty): self
    {
        return new self($this->id, $this->name, $qty, $this->price, $this->options);
    }

    public function toArray(): array
    {
        return [
            'row_id'   => $this->rowId,
            'id'       => $this->id,
            'name'     => $this->name,
            'qty'      => $this->qty,
            'price'    => $this->price,
            'options'  => $this->options,
            'subtotal' => $this->subtotal,
        ];
    }

    private static function generateRowId(string|int $id, array $options): string
    {
        ksort($options);

        return md5($id . serialize($options));
    }
}
