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
            'is_active' => (bool) $this->is_active,
            'address' =>  collect($this->address)->map(function ($addr) {
                return [
                    'city'           => $addr['city'][app()->getLocale()] ?? null,
                    'state'          => $addr['state'][app()->getLocale()] ?? null,
                    'country'        => $addr['country'][app()->getLocale()] ?? null,
                    'street_address' => $addr['street_address'][app()->getLocale()] ?? null,
                ];
            }),
            'created_at' => $this->created_at,
            'categories' => $this->whenLoaded('categories', CategoryResource::collection($this->categories)),
            // 'owner_id' => $this->owner_id,
            // 'owner' => $this->when($this->needToInclude($request, 'shop.owner'), function () {
            //     return new UserResource($this->owner);
            // }),
            // 'settings' => $this->settings,
            // 'notifications' => $this->notifications,
            
        ];
    }
}
