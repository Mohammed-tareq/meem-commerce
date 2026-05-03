<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Marvel\Enums\PromotionMountType;
use Marvel\Enums\PromotionType;

class PromotionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name" => "required|array",
            'name.*' => ['required_with:name', UniqueTranslationRule::for('promotions', 'name')],
            'type' => ['required', Rule::in(PromotionType::getValues())],
            'type_amount' => ['required', Rule::in(PromotionMountType::getValues())],
            'product_ids' => ['required_if:type_amount,' . PromotionMountType::GIFT, 'array', 'min:1'],
            'product_ids.*' => 'exists:products,id',
            'value' => 'required|numeric|min:1',
            'max_discount_amount' => 'sometimes|numeric|min:1',
            'required_quantity_type' => 'required|numeric|min:1',
            'limiter' => 'nullable|date',
            'start_at' => 'nullable|date|before_or_equal:today',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'status' => 'sometimes|in:0,1',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
