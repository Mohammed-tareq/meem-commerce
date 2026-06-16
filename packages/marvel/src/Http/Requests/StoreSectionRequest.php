<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string|max:100',
            'title' => 'required|array',
            'title.*' => ['required', 'string', 'max:50', UniqueTranslationRule::for('sections', 'title')],
            'endpoint' => 'nullable|string',
            'is_active' => 'nullable|in:0,1',
        ];
    }
}
