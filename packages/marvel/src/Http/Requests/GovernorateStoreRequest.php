<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GovernorateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'min:2', 'max:50', UniqueTranslationRule::for('governorates')],
            'name.ar' => ['required', 'string', 'min:2', 'max:50', UniqueTranslationRule::for('governorates')],
            'status' => ['nullable', 'in:1,0'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}