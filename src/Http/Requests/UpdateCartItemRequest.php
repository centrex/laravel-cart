<?php

declare(strict_types = 1);

namespace Centrex\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'instance' => ['nullable', 'string', 'max:100'],
            'qty'      => ['required', 'integer', 'min:1'],
        ];
    }
}
