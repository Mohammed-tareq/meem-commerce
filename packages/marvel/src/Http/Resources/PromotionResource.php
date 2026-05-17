<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class PromotionResource extends Resource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->typeByLang(),
            'value' => $this->value,
            'code' => $this->code,
            'min_order_amount' => $this->min_order_amount,
            'image' => method_exists($this, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('promotions') : null,
            'start_at' => $this->start_at ? $this->start_at->toIso8601String() : null,
            'end_at' => $this->end_at ? $this->end_at->toIso8601String() : null,
            'status' => (bool) $this->status,
            'is_valid' => $this->isValid(),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
