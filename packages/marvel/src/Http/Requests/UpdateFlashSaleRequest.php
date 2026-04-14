<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class UpdateFlashSaleRequest extends FormRequest
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
        // $language = $this->language ?? DEFAULT_LANGUAGE;

        $id = $this->route("flash_sale");
        $rules =  [
            'title'        => ['required', 'array'],
            'title.*'=> ['required','string','min:3' , 'max:70',UniqueTranslationRule::for('flash_sales',"title")->ignore($id)],
            'description'        => ['required', 'array'],
            'description.*'  => ['required', 'string', 'max:1000'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date'],
            'slug'         => ['nullable', 'string'],
        ];
        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
