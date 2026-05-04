<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class FaqResource extends Resource
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
            'id'              => $this->id,
            'faq_title'       => $this->getTranslation('faq_title', app()->getLocale()),
            'faq_description' => $this->getTranslation('faq_description', app()->getLocale()),
            'faq_type'        => $this->faq_type,
            'issued_by'       => $this->issued_by,
            // "shop"            => $this->whenLoaded('shop',ShopResource::make($this->shop)),
//
        ];
    }
}
