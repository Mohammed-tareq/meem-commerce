<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->getTranslation('name', app()->getLocale()),
            'slug'                   => $this->slug,
            'description'            => $this->getTranslation('description', app()->getLocale()),
            'price'                  => $this->roundMoney($this->price),
            'current_price'          => $this->roundMoney($this->current_price),
            'price_after_discount'    => $this->roundMoney($this->price_after_discount),
            'price_after_flash_sale'  => $this->roundMoney($this->price_after_flash_sale),
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
            $this->mergeWhen($this->has_discount, fn() => ['discount_valid' => $this->isDiscountActive()]),
            "images"                 => $this->getmedia('products') ? $this->getMediaImages('products') : [],
            "variants"                => $this->whenLoaded('variations', fn() => $this->getVariants()),
            'reviews'                 => ReviewResource::collection($this->whenLoaded('reviews')),
            $this->mergeWhen($this->relationLoaded('related_products'), fn() => ['related_products' => ProductMiniResource::collection($this->related_products)]),
        ];
    }




    private function getMediaImages($collection)
    {
        return $this->getmedia($collection)->map(function ($media) {
            return $media->getUrl();
        });
    }

    private function getVariants()
    {
        return $this->variations->map(function ($variant) {
            return [
                'id' => $variant->id,
                'price' => $this->roundMoney($variant->price),
                'current_price' => $this->roundMoney($variant->current_price),
                'quantity' => $variant->quantity,
                'height' => $variant->height,
                'width' => $variant->width,
                'length' => $variant->length,
                'weight' => $variant->weight,
                'attributes' => $this->getAttributeName($variant->attributeProducts),
            ];
        });
    }

    private function getAttributeName($attributeProducts)
    {
        return $attributeProducts->map(function ($attrProduct) {
            return [
                'attribute_name' => optional(optional($attrProduct->attributeValue)->attribute)->name,
                'value' => optional($attrProduct->attributeValue)->value,
            ];
        });
    }

    private function roundMoney($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
