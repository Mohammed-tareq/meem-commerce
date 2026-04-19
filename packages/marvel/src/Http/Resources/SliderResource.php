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
            "type" => $this->type,
            "is_active" => (bool)$this->is_active,
            "image" => $this->type === 'image' ? $this->getFirstMediaUrl('sliders-images-secondary') : $this->getFirstMediaUrl('slider-image')
        ];
    }
}
