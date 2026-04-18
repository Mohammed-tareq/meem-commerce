<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Marvel\Enums\ProductStatus;
use Marvel\Enums\ProductType;

class ProductCreateRequest extends FormRequest
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
        $productStatus = [
            ProductStatus::UNDER_REVIEW,
            ProductStatus::APPROVED,
            ProductStatus::REJECTED,
            ProductStatus::PUBLISH,
            ProductStatus::UNPUBLISH,
            ProductStatus::DRAFT,
        ];

        $productType = [
            ProductType::SIMPLE,
            ProductType::VARIABLE
        ];

        return [
            'name'                         => ['required', 'array'],
            'name.*'                       => ['required', 'string', 'max:255' , UniqueTranslationRule::for('products')],
            'description'                  => ['required', 'array'],
            'description.*'                => ['required', 'string', 'max:10000'],
            'price'                        => ['nullable', 'numeric'],
            'sale_price'                   => ['nullable', 'numeric'],
            'product_type'                 => ['required', Rule::in($productType)],
            'categories'                   => ['array', 'required', 'exists:categories,id'],
            'quantity'                     => ['nullable', 'integer'],
            'image'                        => ['array'],
            'status'                       => ['string', Rule::in($productStatus)],
            'height'                       => ['nullable', 'numeric'],
            'length'                       => ['nullable', 'numeric'],
            'width'                        => ['nullable', 'numeric'],
            'in_stock'                     => ['boolean','in:true|false'],
            'has_discount'                 => ['boolean','in:true|false'],
            'has_flash_sale'               => ['boolean','in:true|false'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
