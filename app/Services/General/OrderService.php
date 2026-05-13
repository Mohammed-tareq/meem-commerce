<?php

namespace App\Services\General;

use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\Order;
use Marvel\Services\Pricing\ProductPricingService;

class OrderService
{
    protected $dataArray = [
        'name',
        'user_phone',
        'user_email',
        'address',
        'notes',
    ];


    public function calcInvoicePrice($request)
    {

        try {
            DB::beginTransaction();
            $cart = $this->getCartUser();
            if (!$cart || !$cart->items()->exists()) {
                return null;
            }
            $totalPrice = $cart->items->sum('total_price');

            $totalPrice =  $this->calculatePriceByCoupon($cart, $totalPrice);
            $cart->update(['total_price' => $totalPrice]);
            $order = $this->saveOrderInDatabase($request->only($this->dataArray), $cart);
            $orderItems = $this->createOrderItems($order, $cart);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    protected function saveOrderInDatabase($order, $cart)
    {
        $totalPriceBeforeCoupon = $cart->items->sum('total_price');
        $couponData = $this->getCoupon($cart?->coupon ?? null);
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'name' => $order['name'],
            'user_phone' => $order['user_phone'],
            'user_email' => $order['user_email'],
            'address' => $order['address'],
            'notes' => $order['notes'] ?? null,
            'price' => $totalPriceBeforeCoupon,
            'shipping_price' => null,
            'total_price' => $cart->total_price,
            'coupon' => $couponData?->code ?? null,
            'coupon_discount' => $couponData?->discount ?? null,
            'coupon_discount_type' => $couponData?->discount_type ?? null,
            'coupon_discount_max_amount' => $couponData?->discount_max_amount ?? null,
            'status' => 'pending',
        ]);
        return $order;
    }
    private function createOrderItems($order, $cart)
    {
        foreach ($cart->items as $item) {
            $orderItem = $order->orderItems()->create([
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'product_name' => $item->product->name ?? 'No Name',
                'product_quantity' => $item->quantity ?? 0,
                'product_price' => $item->price,
                'product_total_price' => $item->total_price,
                'product_sku' => $item->product->sku ?? null,
                'shop_name' => $item->product->shops()->first()?->name ?? null,
                'product_flash_sale_price' => $item->product->has_flash_sale ? $item->product->flash_sales()->valid()->where('id', $item->product->flash_sale_id)->first()?->price : null,
                'product_discount_price' => $item->product->has_discount ? $item->product->discount_amount : null,
                'attributes' => $item->attributes ?? null,

            ]);
            if (!$orderItem) {
                return false;
            }
        }
        return true;
    }




    //     public function createTransaction($orderId, $invoiceId, string $paymentType)
    //     {
    //         $transaction = Transaction::create([
    //             'order_id' => $orderId,
    //             'user_id' => auth('web')->user()->id,
    //             'invoice_id' => $invoiceId,
    //             'payment_method' => $paymentType,
    //         ]);
    //         if (!$transaction) {
    //             return false;
    //         }
    //         return $transaction;

    //     }

    // public function sendAdminNotification($order)
    // {
    //     $admins = Admin::active()->whereHas('role', function ($q) {
    //         $q->whereJsonContains('permissions', 'notification');
    //     })->get();
    //     Notification::send($admins, new CreateOrderNotification($order));
    // }



    private function getCartUser()
    {
        return auth()->user()->cart?->load('items.product');
    }

    private function getCoupon($couponCode)
    {
        if ($couponCode) {
            return Coupon::valid()->where('code', $couponCode)->first();
        }
        return null;
    }





    private function calculatePriceByCoupon($cart, $totalPrice)
    {
        if ($cart->coupon !== null) {
            if (!$this->checkCouponUsage($cart->coupon)) {
                return $totalPrice = $this->CalcPriceByCoupon($cart->coupon, $totalPrice);
            }
            return $totalPrice;
        } else {
            return $totalPrice = $this->CalcPriceByCoupon($cart->coupon, $totalPrice);
        }
    }
    private function checkCouponUsage($couponId)
    {
        $coupon = Coupon::valid()->where('code', $couponId)->first();
        if (!$coupon) {
            return false;
        }
        $couponUsage = $coupon->users()->where('user_id', auth()->id())->first();
        return $couponUsage?->pivot?->used_at;
    }
    private function CalcPriceByCoupon($couponId, $price)
    {
        $coupon = Coupon::valid()->where('code', $couponId)->first();
        if (!$coupon) {
            return $price;
        }
        $used = $coupon->users()
            ->wherePivot('user_id', auth()->id())
            ->first();

        if ($used && $used->pivot->used_at) {
            return $price;
        }
        $used  = $coupon->users()->updateExistingPivot(auth()->id(), ['used_at' => now()]);
        return app(ProductPricingService::class)->calculateCouponPrice($coupon, $price);
    }





    // public function clearCart()
    // {
    //     auth('web')->user()->cart->items()->delete();
    // }

    // public function changeOrderStatus($transactionId, $status)
    // {
    //     $transaction = Transaction::where('invoice_id', $transactionId)->first();
    //     if (!$transaction) {
    //         return false;
    //     }
    //     return $transaction->order->update(['status' => $status]);

    // }

    // public function getOrder($transactionId)
    // {
    //     $transaction = Transaction::where('invoice_id', $transactionId)->first();
    //     if (!$transaction) {
    //         return false;
    //     }
    //     return $order = $transaction->order;

    // }
}
