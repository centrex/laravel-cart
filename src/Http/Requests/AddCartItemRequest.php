<?php

declare(strict_types = 1);

namespace Centrex\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'        => ['required'],
            'name'      => ['required', 'string', 'max:255'],
            'qty'       => ['required', 'integer', 'min:1'],
            'price'     => ['required', 'numeric', 'min:0'],
            'options'   => ['nullable', 'array'],
            'options.*' => ['string'],
        ];
    }
}
