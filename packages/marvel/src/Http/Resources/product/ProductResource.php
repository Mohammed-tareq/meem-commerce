<?php

namespace Marvel\Http\Resources;

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
            'created_at'             => $this->created_at ? $this->created_at->toIso8601String() : null,
            'categories'            => $this->whenLoaded('categories', fn () => $this->getCategories($this->categories)),
            // 'shops'                 => $this->whenLoaded('shops', fn() => $this->getShops($this->shops)),
            'flash_sales'          => $this->whenLoaded('flash_sales', fn() => FlashSaleResource::collection($this->flash_sales)),
            "images"                 => $this->getmedia('products') ? $this->getmediaImages('products') : [],
            "variants"                => $this->whenLoaded('variations', fn() => $this->getVariants()),
            $this->mergeWhen($this->relationLoaded('related_products'), fn () => ['related_products' => ProductResource::collection($this->related_products)]),
        ];
    }


    private function getCategories($categories)
    {
        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name', app()->getLocale()),
                'slug' => $category->slug,
            ];
        });
    }

    private function getShops($shops)
    {
        return $shops->map(function ($shop) {
            return [
                'id' => $shop->id,
                'name' => $shop->getTranslation('name', app()->getLocale()),
                'slug' => $shop->slug,
            ];
        });
    }

    private function getmediaImages($collection)
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
                        'price' => $variant->price,
                        'sale_price' => $variant->sale_price,
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
}
