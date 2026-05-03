<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class ProductResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->getTranslation('name', app()->getLocale()),
            'slug'                   => $this->slug,
            'description'            => $this->getTranslation('description', app()->getLocale()), // Array فيه en/ar
            'price'                  => $this->price,
            'price_after_discount'   => $this->price_after_discount,
            'price_after_flash_sale' => $this->price_after_flash_sale,
            'discount_type'          => $this->discount_type,
            'discount_amount'        => $this->discount_amount,
            'start_date'             => $this->start_date,
            'end_date'               => $this->end_date,
            'sku'                    => $this->sku,
            'quantity'               => $this->quantity,
            'sold_quantity'          => $this->sold_quantity ?? 0,
            'in_stock'               => $this->in_stock,
            'status'                 => (bool)$this->status,
            'product_type'           => $this->product_type,
            'height'                 => $this->height,
            'width'                  => $this->width,
            'length'                 => $this->length,
            'weight'                 => $this->weight,
            'has_flash_sale'         => $this->has_flash_sale,
            'has_discount'           => $this->has_discount,
            'banner_id'              => $this->banner_id,
            'shop_id'                => $this->shop_id,
            'created_at'             => $this->created_at ? $this->created_at->toIso8601String() : null,
            "images"                 => $this->getmedia('products') ? $this->getmedia('products')->map(function ($media) {
                return $media->getUrl();
            }) : [],
            $this->mergeWhen($this->relationLoaded('related_products'), function () {
                return [
                    'related_products' => ProductResource::collection($this->related_products),
                ];
            }),
            "variants"                => $this->whenLoaded('variations', function () {
                return $this->variations->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'price' => $variant->price,
                        'sale_price' => $variant->sale_price,
                        'quantity' => $variant->quantity,
                        'height' => $variant->height,
                        'width' => $variant->width,
                        'length' => $variant->length,
                        'weight' => $variant->weight,
                        'attributes' => $variant->attributeProducts->map(function ($attrProduct) {
                            return [
                                'attribute_name' => $attrProduct->attributeValue->attribute->name,
                                'value' => $attrProduct->attributeValue->value,
                            ];
                        }),
                    ];
                });
            }),
        ];
    }
}