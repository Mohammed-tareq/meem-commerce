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
            'id'                     => $this->id,
            'name'                   => $this->getTranslation('name', app()->getLocale()),
            'slug'                   => $this->slug,
            'price'                  => $this->roundMoney($this->price),
            'current_price'          => $this->roundMoney($this->getRawOrComputedValue('current_price')),
            'price_after_discount'    => $this->roundMoney($this->getRawOrComputedValue('price_after_discount')),
            'discount_type'          => $this->discount_type,
            'discount_amount'        => $this->discount_amount,
            'quantity'               => $this->quantity,
            'discount_valid'           =>  (bool) $this->isDiscountActive(),
            'image'                  =>  $this->getmedia('products') ? $this->getmediaImages('products') : [],
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
    private function getmediaImages($collection)
    {
        return $this->getmedia($collection)->map(function ($media) {
            return $media->getUrl();
        });
    }
}
