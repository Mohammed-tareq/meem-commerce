<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class BannerResource extends Resource
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
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'image'       => [
                "desktop" => $this->when($this->getFirstMediaUrl('banner-image-desktop'), $this->getFirstMediaUrl('banner-image-desktop')),
                "mobile" => $this->when($this->getFirstMediaUrl('banner-image-mobile'), $this->getFirstMediaUrl('banner-image-mobile')),
            ],
            "status"   => (bool)$this->status,
            "products"    => $this->whenLoaded('products', function () {
                return ProductResource::collection($this->products);
            }),
        ];

    }


}
