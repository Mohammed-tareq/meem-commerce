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
            "status" => (bool)$this->status,
            "image" => [
                "desktop" => $this->when($this->getFirstMediaUrl('slider-image-desktop'), $this->getFirstMediaUrl('slider-image-desktop')),
                "mobile" => $this->when($this->getFirstMediaUrl('slider-image-mobile'), $this->getFirstMediaUrl('slider-image-mobile')),
            ]
        ];
    }
}
