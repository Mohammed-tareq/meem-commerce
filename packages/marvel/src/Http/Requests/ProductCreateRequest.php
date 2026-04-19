<?php

namespace Marvel\Http\Requests;

use CodeZero\UniqueTranslation\UniqueTranslationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Marvel\Enums\DiscountType;
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

        // $productType = [
        //     ProductType::SIMPLE,
        //     ProductType::VARIABLE,
        // ];

        $discountTypes = DiscountType::getValues();

        return [
            'name'                         => ['required', 'array'],
            'name.*'                       => ['required', 'string', 'max:255', UniqueTranslationRule::for('products')],
            'description'                  => ['required', 'array'],
            'description.*'                => ['required', 'string', 'max:10000'],
            'slug'                         => ['required', 'string', 'max:255', 'unique:products,slug'],
            'price'                        => ['nullable', 'numeric', 'min:0'],
            // 'product_type'                 => ['required', Rule::in($productType)],
            'categories'                   => ['required', 'array', 'exists:categories,id'],
            'quantity'                     => ['required', 'integer', 'min:0'],
            'image'                        => ['required', 'array'],
            'image.*'                      => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'status'                       => ['required', 'string', Rule::in($productStatus)],
            'height'                       => ['nullable', 'numeric'],
            'length'                       => ['nullable', 'numeric'],
            'width'                        => ['nullable', 'numeric'],
            'weight'                       => ['nullable', 'numeric'],
            'in_stock'                     => ["required", 'boolean'],
            'has_discount'                 => ["required", 'boolean'],
            'has_flash_sale'               => ["required", 'boolean'],
            'flash_sale_id'                => ['sometimes', 'exists:flash_sales,id'],
            'discount_type'                => ['sometimes', Rule::in($discountTypes)],
            'amount'                       => ['sometimes', 'numeric', 'min:0'],
            'start_date'                   => ['sometimes', 'date'],
            'end_date'                     => ['sometimes', 'date', 'after_or_equal:start_date'],
            'price_after_discount'         => ['sometimes', 'numeric', 'min:0'],
            'price_after_flash_sale'       => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
