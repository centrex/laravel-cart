<?php

declare(strict_types = 1);

namespace Centrex\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'row_id'   => $this->resource->rowId,
            'id'       => $this->resource->id,
            'name'     => $this->resource->name,
            'qty'      => $this->resource->qty,
            'price'    => $this->resource->price,
            'subtotal' => $this->resource->subtotal,
            'options'  => $this->resource->options,
        ];
    }
}
