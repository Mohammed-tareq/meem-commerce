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
            'discount_type' => $this->type_amount,
            'value' => $this->value,
            'discount' => $this->discount ?? $this->value,
            'code' => $this->code,
            'min_order_amount' => $this->min_order_amount,
            'minimum_order_amount' => $this->minimum_order_amount,
            'required_quantity' => $this->required_quantity_type,
            'apply_to' => $this->apply_to,
            'products' => $this->whenLoaded('products'),
            'gift_products' => $this->whenLoaded('giftProducts'),
            'image' => method_exists($this, 'getFirstMediaUrl') ? $this->getFirstMediaUrl('promotions') : null,
            'start_at' => $this->start_at ? $this->start_at->toIso8601String() : null,
            'end_at' => $this->end_at ? $this->end_at->toIso8601String() : null,
            'status' => (bool) $this->status,
            'is_valid' => $this->isValid(),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
