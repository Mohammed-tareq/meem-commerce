<?php


namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class CategoryUpdateRequest extends FormRequest
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
        $id = $this->route('category');
        return [
            'name'         => ['sometimes', 'array'],
            'name.*'       => ['sometimes', 'string' , UniqueTranslationRule::for('categories')->ignore($id)],
            'image'        => ['sometimes', 'file', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'parent_id'    => ['nullable', 'integer', 'exists:categories,id'],
            'shops_id' => ['sometimes', 'array'],
            'shops_id.*' => ['sometimes', 'integer' ,"exists:shops,id"],
            'details'      => ['sometimes', 'string','min:3','max:2500'],
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
            'name.*.string'         => 'Name is not a valid string',
            'name.*.max:255'        => 'Name can not be more than 255 character',
            'image.string'        => 'image is not a valid string',
            'banner_image.string' => 'Banner image is not a valid image',
            'parent.integer'      => 'Parent is not a valid integer',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
