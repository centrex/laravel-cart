<?php

declare(strict_types = 1);

namespace Centrex\Cart\Http\Controllers;

use Centrex\Cart\Cart;
use Centrex\Cart\Exceptions\CartItemNotFoundException;
use Centrex\Cart\Http\Requests\{AddCartItemRequest, UpdateCartItemRequest};
use Centrex\Cart\Http\Resources\{CartItemResource, CartResource};
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    public function __construct(private readonly Cart $cart) {}

    /** GET /api/cart */
    public function index(): CartResource
    {
        return new CartResource($this->cartPayload());
    }

    /** POST /api/cart/items */
    public function store(AddCartItemRequest $request): CartItemResource
    {
        $item = $this->cart->add(
            id:      $request->input('id'),
            name:    $request->string('name')->toString(),
            qty:     (int) $request->input('qty'),
            price:   (float) $request->input('price'),
            options: (array) $request->input('options', []),
        );

        return new CartItemResource($item);
    }

    /** PUT /api/cart/items/{rowId} */
    public function update(UpdateCartItemRequest $request, string $rowId): CartItemResource|JsonResponse
    {
        try {
            $item = $this->cart->update($rowId, (int) $request->input('qty'));
        } catch (CartItemNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new CartItemResource($item);
    }

    /** DELETE /api/cart/items/{rowId} */
    public function destroy(string $rowId): JsonResponse
    {
        try {
            $this->cart->remove($rowId);
        } catch (CartItemNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /** DELETE /api/cart */
    public function clear(): JsonResponse
    {
        $this->cart->clear();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function cartPayload(): array
    {
        return [
            'instance' => $this->cart->getInstanceName(),
            'items'    => $this->cart->content(),
            'count'    => $this->cart->count(),
            'lines'    => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
            'tax'      => $this->cart->tax(),
            'total'    => $this->cart->total(),
        ];
    }
}
