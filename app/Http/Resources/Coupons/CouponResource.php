<?php

namespace App\Http\Resources\Coupons;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
             return [
            'id'          => $this->id,
            'name'       => $this->getTranslation('name', app()->getLocale()),
            'image'       =>[
                'desktop' => $this?->getFirstMediaUrl('coupons-desktop'),
                'mobile' => $this?->getFirstMediaUrl('coupons-mobile'),
            ],

        ];
    }
}
