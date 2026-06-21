<?php

namespace Marvel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FastCheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'user_phone' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'array'],
            'notes' => ['nullable', 'string'],
            'governorate_id' => ['required', 'integer', 'exists:governorates,id'],
            'selected_promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
            'selected_gift_product_id' => ['nullable', 'integer', 'exists:products,id'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
