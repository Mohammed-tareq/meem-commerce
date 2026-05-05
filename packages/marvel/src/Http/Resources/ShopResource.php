<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class ShopResource extends Resource
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
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->getFirstMediaUrl('shop-image'),
            'logo' => $this->getFirstMediaUrl('shop-logo'),
            'status' => (bool) $this->status,
            'address' =>  collect($this->address)->map(function ($addr) {
                return [
                    'city'           => $addr['city'][app()->getLocale()] ?? null,
                    'state'          => $addr['state'][app()->getLocale()] ?? null,
                    'country'        => $addr['country'][app()->getLocale()] ?? null,
                    'street_address' => $addr['street_address'][app()->getLocale()] ?? null,
                ];
            }),
            'created_at' => $this->created_at,
            // $this->mergeWhen(
            //     !request()->routeIs('general-shop-index') && !request()->routeIs('shops.index'),
            //     [
            //         'categories' => $this->whenLoaded('categories', CategoryResource::collection($this->categories)),
            //         'products_count' => $this->whenCounted('products', 0),

            //     ]
            // )
        ];
    }
}
