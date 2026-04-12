<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShopUpdateRequest extends FormRequest
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
        $id = $this->route('shop');
        return [
             'name'                   => ['required', 'array'],
            'name.*'                 => ['required', 'string', 'max:50',UniqueTranslationRule::for('shops')->ignore($id)],
            'description'            => ['required', 'array'],
            'description.*'          => ['required', 'string', 'max:2000'],
            'logo'                   => ['sometimes', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'cover_image'            => ['sometimes', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'settings'               => ['sometimes', 'array'],
            'address'                => ['sometimes', 'array'],
            'is_active'              => ['nullable', "in:1,true"],
            // 'admin_commission_rate'  => ['nullable', 'numeric'],
            // 'total_earnings'         => ['nullable', 'numeric'],
            // 'withdrawn_amount'       => ['nullable', 'numeric'],
            // 'current_balance'        => ['nullable', 'numeric'],
            // 'categories'             => ['array'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
