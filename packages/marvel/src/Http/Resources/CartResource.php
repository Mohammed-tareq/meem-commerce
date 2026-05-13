<?php

namespace Marvel\Http\Resources;

use Illuminate\Http\Request;

class CartResource extends Resource
{
    public function toArray(Request $request)
    {
        $items = $this->whenLoaded('items');
        $totalQuantity = $items ? $items->sum('quantity') : null;


        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_items' => $items ? $items->count() : null,
            'total_quantity' => $totalQuantity,
            'total_price' => $this->total_price,
            'items' => CartItemResource::collection($items),
        ];
    }
}
