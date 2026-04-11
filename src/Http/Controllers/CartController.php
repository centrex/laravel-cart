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
    public function index(\Illuminate\Http\Request $request): CartResource
    {
        $cart = $this->resolveCart($request);

        return new CartResource($this->cartPayload($cart));
    }

    /** POST /api/cart/items */
    public function store(AddCartItemRequest $request): CartItemResource
    {
        $item = $this->resolveCart($request)->add(
            id: $request->input('id'),
            name: $request->string('name')->toString(),
            qty: (int) $request->input('qty'),
            price: (float) $request->input('price'),
            options: (array) $request->input('options', []),
        );

        return new CartItemResource($item);
    }

    /** PUT /api/cart/items/{rowId} */
    public function update(UpdateCartItemRequest $request, string $rowId): CartItemResource|JsonResponse
    {
        try {
            $item = $this->resolveCart($request)->update($rowId, (int) $request->input('qty'));
        } catch (CartItemNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return new CartItemResource($item);
    }

    /** DELETE /api/cart/items/{rowId} */
    public function destroy(\Illuminate\Http\Request $request, string $rowId): JsonResponse
    {
        try {
            $this->resolveCart($request)->remove($rowId);
        } catch (CartItemNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /** DELETE /api/cart */
    public function clear(\Illuminate\Http\Request $request): JsonResponse
    {
        $this->resolveCart($request)->clear();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function cartPayload(Cart $cart): array
    {
        return [
            'instance' => $cart->getInstanceName(),
            'items'    => $cart->content(),
            'count'    => $cart->count(),
            'lines'    => $cart->lines(),
            'subtotal' => $cart->subtotal(),
            'tax'      => $cart->tax(),
            'total'    => $cart->total(),
        ];
    }

    private function resolveCart(\Illuminate\Http\Request $request): Cart
    {
        $instance = (string) ($request->input('instance') ?: $request->query('instance') ?: config('laravel-cart.default_instance', 'default'));

        return $this->cart->instance($instance);
    }
}
