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
            ProductStatus::PUBLISH,
            ProductStatus::UNPUBLISH,
        ];

        $productType = ProductType::getValues();
        $discountTypes = DiscountType::getValues();

        return [
            'name'                         => ['sometimes', 'array'],
            'name.*'                       => ['sometimes', 'string', 'max:255', UniqueTranslationRule::for('products')->ignore($this->route('product'))],
            'description'                  => ['sometimes', 'array'],
            'description.*'                => ['sometimes', 'string', 'max:10000'],
            'product_type'                 => ['sometimes', Rule::in($productType)],
            'price'                        => ['sometimes', 'numeric', 'min:0', 'required_if:product_type,' . ProductType::SIMPLE],
            'shop_id'                      => ['sometimes', 'exists:shops,id'],
            'categories'                   => ['sometimes', 'array'],
            'categories.*'                 => ['integer', 'exists:categories,id'],
            'quantity'                     => ['sometimes', 'integer', 'min:1'],
            'images'                       => ['sometimes', 'array'],
            'images.*'                     => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', "max:2048"],
            'status'                       => ['sometimes', Rule::in(ProductStatus::getValues())],
            'pieces'                       => ['sometimes', 'integer', 'min:1'],
            'height'                       => ['nullable', 'numeric'],
            'length'                       => ['nullable', 'numeric'],
            'width'                        => ['nullable', 'numeric'],
            'weight'                       => ['nullable', 'numeric'],
            'in_stock'                     => ['sometimes', 'in:true,false,1,0'],
            'has_discount'                 => ['sometimes', 'in:true,false,1,0'],
            'has_flash_sale'               => ['sometimes', 'in:true,false,1,0'],
            'flash_sale_id'                => ['required_if:has_flash_sale,1', 'exists:flash_sales,id'],
            'discount_type'                => ['required_if:has_discount,1', Rule::in($discountTypes)],
            'discount_amount'              => ['required_if:has_discount,1', 'numeric', 'min:1'],
            'discount_status'              => ['required_if:has_discount,1', 'in:true,false,1,0'],
            'start_date'                   => ['sometimes', 'date'],
            'end_date'                     => ['sometimes', 'date', 'after_or_equal:start_date'],
            'banner_id'                    => ['sometimes', 'exists:banners,id'],

            // variants
            'variants'                     => ['sometimes', 'array'],
            'variants.*.id'                => ['sometimes', 'exists:product_variants,id'], // مهم علشان نعرف أي Variant يتعدل
            'variants.*.price'             => ['sometimes', 'numeric', 'min:0'],
            'variants.*.sale_price'        => ['sometimes', 'numeric', 'min:0'],
            'variants.*.quantity'          => ['sometimes', 'integer', 'min:0'],
            'variants.*.weight'            => ['sometimes', 'numeric', 'min:0'],
            'variants.*.length'            => ['sometimes', 'numeric', 'min:0'],
            'variants.*.width'             => ['sometimes', 'numeric', 'min:0'],
            'variants.*.height'            => ['sometimes', 'numeric', 'min:0'],
            'variants.*.attribute_values'  => ['sometimes', 'array'],
            'variants.*.attribute_values.*' => ['integer', 'exists:attribute_values,id'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}