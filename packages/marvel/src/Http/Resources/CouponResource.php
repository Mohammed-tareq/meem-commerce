<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class CouponResource extends Resource
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
            'id'            => $this->id,
            'code'          => $this->code,
            'name'          => $this->getTranslation('name', app()->getLocale()),
            'image'         => $this->getFirstMediaUrl('coupons'),
            'discount'      => $this->discount,
            'discount_type' => $this->typeByLang(), // percentage أو fixed
            'max_discount_amount' => $this->max_discount_amount,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'limiter'       => $this->limiter,
            'used'          => $this->used,
            'status'        => (bool) $this->status,
            'is_valid'      => $this->isValid(),
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
