<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|array',
            'title.*' => ['sometimes', 'string', 'max:50' , UniqueTranslationRule::for('sections', 'title')->ignore($this->route('section'))],
            'order' => 'sometimes|integer',
            'is_active' => 'sometimes|in:0,1',
            'title_visible' => 'sometimes|in:0,1',
            'endpoint' => 'sometimes|string|max:255',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
