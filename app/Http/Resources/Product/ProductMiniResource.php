<?php

namespace App\Http\Resources\Product;

use App\Traits\HasProductFilters;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\Cast\Bool_;

class ProductMiniResource extends JsonResource
{
    use HasProductFilters;
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
            'slug' => $this->slug,
            'price' => $this->roundMoney($this->price),
            'has_variants' => $this->product_type !== 'simple' ? true : false,
            'current_price' => $this->roundMoney($this->getRawOrComputedValue('current_price')),
            'is_fast_shipping_available' => (bool) $this->is_fast_shipping_available,
            'price_after_discount' => $this->roundMoney($this->getRawOrComputedValue('price_after_discount')),
            'price_after_flash_sale' => $this->roundMoney($this->getRawOrComputedValue('price_after_flash_sale')),
            'has_discount' => $this->has_discount,
            'discount_type' => $this->discount_type,
            'discount_amount' => $this->roundMoney($this->discount_amount),
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
            'weight' => $this->weight,
            'quantity' => (int) $this->stock_quantity,
            'in_stock'               =>(bool) $this->in_stock,
            'discount_valid' => (bool) $this->isDiscountActive(),
            'ratings' => round((float) ($this->reviews_avg_rating ?? $this->reviews()->avg('rating') ?? 0), 2),
            'image' => [
                'thumbnail' => $this->getFirstMediaUrl('products'),
                'original' => $this->getMediaImages('products'),
            ],
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