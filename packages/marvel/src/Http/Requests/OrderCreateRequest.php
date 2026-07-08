<?php

namespace Marvel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class OrderCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'user_phone' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'array'],
            'notes' => ['nullable', 'string'],
            'selected_promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
            'selected_gift_product_id' => ['nullable', 'integer', 'exists:products,id'],
            'type' => ['nullable', 'in:mobile,web'],
            'fulfillment_type' => ['nullable', 'string', 'in:delivery,pickup'],
            'payment_method' => ['nullable', 'string', 'in:online,cod,pay_at_cashier'],
            'gateway' => ['nullable', 'string', 'max:50'],
            // 'pickup_location_id' => [
            //     'nullable',
            //     'integer',
            //     Rule::requiredIf(fn () => $this->input('fulfillment_type') === 'pickup'),
            //     'exists:resources,id',
            // ],
        ];
    }


    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}