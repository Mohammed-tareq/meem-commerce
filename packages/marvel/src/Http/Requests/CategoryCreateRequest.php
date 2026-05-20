<?php


namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class CategoryCreateRequest extends FormRequest
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
            'name' => ['required', 'array'],
            'name.*' => ['required', 'string', UniqueTranslationRule::for('categories', 'name')],
            'image-desktop' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'image-mobile' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],   
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'shops_id' => ['required', 'array'],
            'shops_id.*' => ['required', 'integer', "exists:shops,id"],
            'details'      => ['sometimes', 'string', 'min:3', 'max:2500'],
        ];
    }

    /**
     * Get the error messages that apply to the request parameters.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Name field is required',
            'name.unique' => 'Name already exists',
            'name.*.string' => 'Name is not a valid string',
            'name.*.max:255' => 'Name can not be more than 255 character',
            'icon.string' => 'Icon is not a valid string',
            'image.string' => 'Image is not a valid image',
            'banner_image.string' => 'Banner image is not a valid image',
            'details.string' => 'Details is not a valid string',
            'parent.integer' => 'Parent is not a valid integer',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
