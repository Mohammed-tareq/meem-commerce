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

class ProductUpdateRequest extends FormRequest
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
            'name'                         => ['sometimes', 'array'],
            'name.*'                       => ['sometimes', 'string', 'max:255', UniqueTranslationRule::for('products')->ignore($this->route('product'))],
            'description'                  => ['sometimes', 'array'],
            'description.*'                => ['sometimes', 'string', 'max:10000'],
            'price'                        => ['sometimes', 'numeric', 'min:0'],
            // 'product_type'                 => ['sometimes', Rule::in($productType)],
            'categories'                   => ['sometimes', 'array', 'exists:categories,id'],
            'quantity'                     => ['sometimes', 'integer', 'min:0'],
            'image'                        => ['sometimes', 'array'],
            'status'                       => ['sometimes', 'string', Rule::in($productStatus)],
            'height'                       => ['sometimes', 'numeric'],
            'length'                       => ['sometimes', 'numeric'],
            'width'                        => ['sometimes', 'numeric'],
            'weight'                       => ['sometimes', 'numeric'],
            'in_stock'                     => ["sometimes", 'boolean'],
            'has_discount'                 => ["sometimes", 'boolean'],
            'has_flash_sale'               => ["sometimes", 'boolean'],
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