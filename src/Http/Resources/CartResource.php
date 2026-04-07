<?php

declare(strict_types = 1);

namespace Centrex\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'instance' => $this->resource['instance'],
            'items'    => CartItemResource::collection($this->resource['items']->values()),
            'count'    => $this->resource['count'],
            'lines'    => $this->resource['lines'],
            'subtotal' => $this->resource['subtotal'],
            'tax'      => $this->resource['tax'],
            'total'    => $this->resource['total'],
        ];
    }
}
