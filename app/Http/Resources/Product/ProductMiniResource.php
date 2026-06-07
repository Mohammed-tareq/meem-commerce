<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductMiniResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            // 'slug'                   => $this->getTranslation('slug', app()->getLocale()),
            'price' => $this->roundMoney($this->price),
            'current_price' => $this->roundMoney($this->getRawOrComputedValue('current_price')),
            'price_after_discount' => $this->roundMoney($this->getRawOrComputedValue('price_after_discount')),
            'price_after_flash_sale' => $this->roundMoney($this->getRawOrComputedValue('price_after_flash_sale')),
            'has_discount' => $this->has_discount,
            'discount_type' => $this->discount_type,
            'discount_amount' => $this->discount_amount,
            'quantity' => $this->stock_quantity,
            'discount_valid' => (bool) $this->isDiscountActive(),
            'ratings' => $this->reviews()->avg('rating') ?? 0,
            'image' => [
                'thumbnail' => $this->getFirstMediaUrl('products'),
                'original' => $this->getMediaImages('products'),
            ],
            $this->mergeWhen(request()->routeIs('product-discount-ending-today-or-low-stock'), [
                'badges' => $this->badges[0] ?? null,
            ]),
        ];
    }




    private function roundMoney($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

    private function getRawOrComputedValue(string $key)
    {
        $attributes = $this->getAttributes();

        return array_key_exists($key, $attributes) ? $attributes[$key] : $this->{$key};
    }
    private function getMediaImages($collection)
    {
        $media = $this->getMedia($collection);


        // Return all media URLs except the first (used as 'original')
        return $media->slice(1)
            ->map(function ($m) {
                return $m->getUrl();
            });
    }
}
