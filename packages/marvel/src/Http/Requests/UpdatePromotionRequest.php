<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Marvel\Enums\PromotionType;

class UpdatePromotionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "name"=> "sometimes|array",
            'name.*' => ['required_with:name', UniqueTranslationRule::for('promotions','name')->ignore($this->route('promotion'))],
            'type' => ['sometimes', Rule::in(PromotionType::getValues())],
            'value' => 'sometimes|numeric|min:1',
            'min_order_amount' => 'nullable|numeric|min:1',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
