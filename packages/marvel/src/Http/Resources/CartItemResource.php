<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class CartItemResource extends Resource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total_price' => $this->total_price,
            'attributes' => $this?->attributes,
            // 'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
