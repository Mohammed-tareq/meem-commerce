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
            'total_items' => $items ? $items->count() : null,
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
            'items' => CartItemResource::collection($items),
        ];
    }
}