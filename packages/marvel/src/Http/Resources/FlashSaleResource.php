<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

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
            "image" => $this->getFirstMediaUrl('flash-sales-image'),
            $this->mergeWhen(!request()->routeIs('home'), function () {
                return [
                    "description" => $this->getTranslation("description", app()->getLocale()),
                    'start_date' => $this->start_date,
                    'end_date'   => $this->end_date,
                    "status" => (bool) $this->status,
                    "is_valid" => $this->isValid(),
                    "type" => $this->typeByLang(),
                    "discount" => $this->discount,
                    "max_discount_amount" => $this->max_discount_amount,
                    "created_at" => $this->created_at->format('Y-m-d'),
                ];
            }),

            $this->mergeWhen(request()->routeIs('flash-sale.show'), function () {
                return [
                    "products" => $this->whenLoaded('products', ProductResource::collection($this->products)),
                ];
            }),
        ];
    }
}
