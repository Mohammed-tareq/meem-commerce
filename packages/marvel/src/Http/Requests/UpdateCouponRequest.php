<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Marvel\Enums\CouponType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Marvel\Enums\DiscountType;

class UpdateCouponRequest extends FormRequest
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
            "name" => "sometimes|array",
            'name.*' => ['required_with:name', UniqueTranslationRule::for('coupons', 'name')->ignore($this->route('coupon'))],
            'discount'      => 'sometimes|numeric|min:0',
            'discount_type' => ['sometimes', Rule::in(DiscountType::getValues())],
            'max_discount_amount' => [
                'required_if:discount_type,percentage',
                'numeric',
                'min:1'
            ],
            
            'start_date'    => 'sometimes|date_format:Y-m-d',
            'end_date'      => 'sometimes|date_format:Y-m-d|after_or_equal:start_date',
            'limiter'       => 'nullable|integer|min:0',
            'status'        => 'sometimes|in:1,0',

        ];
    }



    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
