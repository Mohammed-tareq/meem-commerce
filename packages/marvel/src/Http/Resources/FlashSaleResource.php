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
            "status" => (bool) $this->status,
            "type" => $this->typeByLang(),
            "discount" => $this->discount,
            "max_discount_amount"=> $this->max_discount_amount,
            "created_at" => $this->created_at->format('Y-m-d'),
            "products" => $this->whenLoaded('products', ProductResource::collection($this->products)),
        ];
    }
}
