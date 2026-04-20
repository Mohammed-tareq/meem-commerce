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
            'name'          => $this->getTranslation('name',app()->getLocale()),
            'discount'      => $this->discount,
            'discount_type' => $this->typeByLang(), // percentage أو fixed
            'start_date'    => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date'      => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'limiter'       => $this->limiter,
            'used'          => $this->used,
            'status'        => (bool) $this->status,
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
