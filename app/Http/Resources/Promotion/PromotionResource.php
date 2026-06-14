<?php

namespace App\Http\Resources\Promotion;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->getTranslation('name', app()->getLocale()),
            'slug' => $this->slug,
            "status" => (bool)$this->status,
            "image" => [
                "desktop" => $this->when($this->getFirstMediaUrl('promotions-desktop'), $this->getFirstMediaUrl('promotions-desktop')),
                "mobile" => $this->when($this->getFirstMediaUrl('promotions-mobile'), $this->getFirstMediaUrl('promotions-mobile')),
            ]
        ];
    }
}
