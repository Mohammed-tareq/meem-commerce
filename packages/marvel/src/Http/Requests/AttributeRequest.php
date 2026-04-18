<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class AttributeRequest extends FormRequest
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
        $id = $this->route("attribute");
        return [
            'name'        => ['required', 'array'],
            'name.*'      => ['required', 'string','min:2','max:50',UniqueTranslationRule::for('attributes')->ignore($id)],
            'shop_id'     => ['sometimes', 'exists:shops,id'],
            'values' => [
                'sometimes',
                'array',
                'distinct',
            ],
            'values.*' => [
                'sometimes',
                'array',
                'distinct',
            ],
            'values.*.value.*' => [
                'sometimes',
                'string',
                'min:2',
                'max:50',
            
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
