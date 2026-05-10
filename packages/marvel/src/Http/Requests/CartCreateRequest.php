<?php

namespace Marvel\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'item' => ['required', 'array', 'min:1'],
            'item.product_id' => ['required', 'integer', 'exists:products,id'],
            'item.quantity' => ['required', 'integer', 'min:1'],
            'item.product_variant_id' => ['sometimes', 'integer', 'exists:product_variants,id'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
