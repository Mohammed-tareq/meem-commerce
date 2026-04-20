<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class CartResource extends Resource
{
    public function toArray(Request $request)
    {
        $items = $this->whenLoaded('items');
        $totalQuantity = $items ? $items->sum('quantity') : null;
        $totalPrice = $items ? $items->sum('total_price') : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items' => CartItemResource::collection($items),
            'total_items' => $items ? $items->count() : null,
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
