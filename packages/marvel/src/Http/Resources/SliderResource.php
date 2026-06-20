<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class SliderResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {


        return [
            "id" => $this->id,
            "title" => $this->getTranslation('title', app()->getLocale()),
            "slug" => $this->slug,
            "status" => (bool)$this->status,
            "order" => $this->order,
            "image" => [
                "desktop" => $this->getFirstMediaUrl('sliders-desktop') ?: $this->getFirstMediaUrl('slider-image-desktop'),
                "mobile" => $this->getFirstMediaUrl('sliders-mobile') ?: $this->getFirstMediaUrl('slider-image-mobile'),
            ],
            "products" => $this->whenLoaded('products', function () {
                return $this->products->map(fn($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'status' => $product->status,
                    'image' => [
                        'thumbnail' => $product->getFirstMediaUrl('products'),
                    ],
                ]);
            }),
        ];
    }
}
