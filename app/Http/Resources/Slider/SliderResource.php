<?php

namespace App\Http\Resources\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
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
            "title" => $this->getTranslation('title', app()->getLocale()),
            "status" => (bool)$this->status,
            "image" => [
                "desktop" => $this->getFirstMediaUrl('sliders-desktop'),
                "mobile" => $this->getFirstMediaUrl('sliders-mobile'),
            ]
        ];
    }
}
