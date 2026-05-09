<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class BrandResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name', app()->getLocale()),
            'slug' => $this->slug,
            'image' => $this->getFirstMediaUrl('brands'),
            'details' => $this->getTranslation('details', app()->getLocale()),
            'status' => (bool) $this->status,
        ];
    }
}
