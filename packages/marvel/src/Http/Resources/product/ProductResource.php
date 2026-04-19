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
            'amount'                 => $this->amount,
            'start_date'             => $this->start_date ,
            'end_date'               => $this->end_date ,
            'sku'                    => $this->sku,
            'quantity'               => $this->quantity,
            'sold_quantity'          => $this->sold_quantity ?? 0,
            'in_stock'               => $this->in_stock,
            'status'                 => $this->status,
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
            'updated_at'             => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
