<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GovernorateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['sometimes', 'integer', 'exists:countries,id'],
            'name' => ['sometimes', 'array'],
            'name.en' => ['sometimes', 'string', 'min:2', 'max:50', UniqueTranslationRule::for('governorates')->ignore($this->route('governorate'))],
            'name.ar' => ['sometimes', 'string', 'min:2', 'max:50', UniqueTranslationRule::for('governorates')->ignore($this->route('governorate'))],
            'status' => ['nullable', 'boolean'],
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}