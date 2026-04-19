<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Marvel\Database\Models\FlashSale;
use Marvel\Enums\FlashSaleType;

class CreateFlashSaleRequest extends FormRequest
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
            'title'        => ['required', 'array'],
            'title.*'=> ['required','string','min:3' , 'max:70',UniqueTranslationRule::for('flash_sales',"title")],
            'description'        => ['required', 'array'],
            'description.*'  => ['required', 'string', 'max:1000'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date'],
            'slug'         => ['nullable', 'string'],
            'type'=> ['required',Rule::in(FlashSaleType::getValues())],
            'value'=> ['required','numeric','min:0'],

//            'language'     => ['nullable', 'string'],
            // 'image'        => ['nullable', 'array'],
            // 'cover_image'  => ['nullable', 'array'],
//            'sale_builder' => ['nullable', 'array']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
