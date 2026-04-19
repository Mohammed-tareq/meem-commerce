<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;
use Marvel\Helper\ResourceHelpers;

class FlashSaleResource extends Resource
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
            "title" => $this->getTranslation("title", app()->getLocale()),
            "slug" => $this->slug,
            "description" => $this->getTranslation("description", app()->getLocale()),
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            "sale_status" => (bool) $this->sale_status,
            "type" => $this->typeByLang(),
            "products" => $this->whenLoaded('products', ProductResource::collection($this->products)),
            "value" => $this->value,
            "created_at" => $this->created_at->format('Y-m-d'),
            // "rate" => $this->rate,
            //            "sale_builder" => $this->sale_builder,
            // "image" => $this->image,
            // "cover_image" => $this->cover_image,
            //            "language" => $this->language,
            //            "deleted_at" => $this->deleted_at,
            //            "updated_at" => $this->updated_at,
        ];
    }
}
