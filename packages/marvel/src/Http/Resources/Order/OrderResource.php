<?php

namespace Marvel\Http\Resources\Order;

use Illuminate\Http\Request;
use Marvel\Http\Resources\Resource;

class OrderResource extends Resource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'tracking_number' => $this->tracking_number,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'shipping_method' => $this->shipping_method,
            'expected_delivery_at' => $this->expected_delivery_at?->toIso8601String(),
            'customer' => $this->when($this->relationLoaded('user') && $this->user, [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone_number,
            ]),
            'customer_name' => $this->name,
            'customer_phone' => $this->user_phone,
            'customer_email' => $this->user_email,
            'address' => $this->address,
            'notes' => $this->notes,
            'price' => $this->roundMoney($this->price),
            'shipping_price' => $this->roundMoney($this->shipping_price),
            'total_price' => $this->roundMoney($this->total_price),
            'coupon' => $this->coupon,
            'coupon_discount' => $this->roundMoney($this->coupon_discount),
            'promotion' => $this->promotion_id ? [
                'id' => $this->promotion_id,
                'code' => $this->promotion_code,
                'type' => $this->promotion_type,
                'discount' => $this->roundMoney($this->promotion_discount),
            ] : null,
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'transactions' => OrderTransactionResource::collection($this->whenLoaded('transactions')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    private function roundMoney(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
