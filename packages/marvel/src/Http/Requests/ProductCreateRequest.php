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
            // ProductStatus::UNDER_REVIEW,
            // ProductStatus::APPROVED,
            // ProductStatus::REJECTED,
            // ProductStatus::PUBLISH,
            // ProductStatus::UNPUBLISH,
            // ProductStatus::DRAFT,
        ];

        $productType = ProductType::getValues();

        $discountTypes = DiscountType::getValues();

        return [
            'name'                         => ['required', 'array'],
            'name.*'                       => ['required', 'string', 'max:255', UniqueTranslationRule::for('products')],
            'description'                  => ['required', 'array'],
            'description.*'                => ['required', 'string', 'max:10000'],
            'price'                        => ['sometimes', 'numeric', 'min:0', 'required_if:product_type,' . ProductType::SIMPLE],
            'product_type'                 => ['required', Rule::in($productType)],
            'shop_id'                      => ['required', 'exists:shops,id'],
            'categories'                   => ['required', 'array'],
            'categories.*'                 => ['integer', 'exists:categories,id'],
            'quantity'                     => ['sometimes   ', 'integer', 'min:1'],
            // 'images'                        => ['required', 'array'],
            // 'images.*'                      => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'pieces'                       => ['sometimes', 'integer', 'min:1'],
            'status'                       => ['sometimes', 'in:1,0'],
            'height'                       => ['nullable', 'numeric'],
            'length'                       => ['nullable', 'numeric'],
            'width'                        => ['nullable', 'numeric'],
            'weight'                       => ['nullable', 'numeric'],
            'in_stock'                     => ["required", 'in:1,0'],
            'has_discount'                 => ["required", 'in:true,false,1,0'],
            'has_flash_sale'               => ["required", 'in:true,false,1,0'],
            'flash_sale_id'                => ['required_if:has_flash_sale,1', 'exists:flash_sales,id'],
            'discount_type'                => ['required_if:has_discount,1', Rule::in($discountTypes)],
            'discount_amount'              => ['required_if:has_discount,1', 'numeric', 'min:1'],
            'discount_status'              => ['required_if:has_discount,1', 'in:1,0'],
            'start_date'                   => ['sometimes', 'date'],
            'end_date'                     => ['sometimes', 'date', 'after_or_equal:start_date'],
            'banner_id'                    => ['sometimes', 'exists:banners,id'],

            // variants (for variable products)
            'variants'                     => ['sometimes', 'array'],
            'variants.*.price'             => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.quantity'          => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.weight'            => ['sometimes', 'numeric', 'min:0'],
            'variants.*.length'            => ['sometimes', 'numeric', 'min:0'],
            'variants.*.width'             => ['sometimes', 'numeric', 'min:0'],
            'variants.*.height'            => ['sometimes', 'numeric', 'min:0'],
            'variants.*.attribute_values'  => ['required_with:variants', 'array'],
            'variants.*.attribute_values.*' => ['integer', 'exists:attribute_values,id'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
