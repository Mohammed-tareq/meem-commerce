<?php

namespace App\Services\General;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\CouponUsage;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Transaction;
use Marvel\Services\Pricing\ProductPricingService;

class OrderService
{
    private const DEFAULT_PER_PAGE = 15;

    private const MAX_PER_PAGE = 100;

    protected $dataArray = [
        'name',
        'user_phone',
        'user_email',
        'address',
        'notes',
    ];


    public function paginateForUser(Request $request): LengthAwarePaginator
    {
        $limit = $this->getLimit($request);
        $userId = (int) $request->user()->id;

        return Order::query()
            ->forUser($userId)
            ->with($this->orderListRelations())
            ->paginate($limit)
            ->withQueryString();
    }

    /**
     * @return array<int|string, mixed>
     */
    private function orderListRelations(): array
    {
        return [
            'shop',
            'orderItems.product.media',
            'orderItems.productVariant.attributeProducts.attributeValue',
        ];
    }

    private function getLimit(Request $request): int
    {
        $limit = (int) $request->get('limit', self::DEFAULT_PER_PAGE);

        if ($limit <= 0) {
            return self::DEFAULT_PER_PAGE;
        }

        return min($limit, self::MAX_PER_PAGE);
    }

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
            DB::commit();
            return $cart->total_price;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }
    public function addItemsInOrder($request)
    {
        try {
            DB::beginTransaction();
            $cart = $this->getCartUser();
            if (!$cart || !$cart->items()->exists()) {
                return null;
            }
            $order = $this->saveOrderInDatabase($request->only($this->dataArray), $cart);
            if (!$order) {
                DB::rollBack();
                return null;
            }
            if (!$this->createOrderItems($order, $cart)) {
                DB::rollBack();
                return null;
            }
            DB::commit();
            return $order;
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




    public function createTransaction($orderId, $invoiceId, string $paymentType)
    {
        $transaction = Transaction::create([
            'order_id' => $orderId,
            'user_id' => auth()->user()->id,
            'invoice_id' => $invoiceId,
            'payment_method' => $paymentType,
        ]);
        if (!$transaction) {
            return false;
        }
        return $transaction;
    }





    private function getCartUser()
    {
        return Cart::query()
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->with(['items.product', 'items.productVariant'])
            ->first();
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
        return CouponUsage::where('coupon_id', $coupon->id)
            ->where('user_id', auth()->id())
            ->whereNotNull('used_at')
            ->exists();
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
        return app(ProductPricingService::class)->calculateCouponPrice($coupon, $price);
    }

    public function clearCart(?int $userId = null)
    {
        $targetUserId = $userId ?? auth()->id();
        if (!$targetUserId) {
            return false;
        }

        $cart = Cart::query()->where('user_id', $targetUserId)->first();
        if (!$cart) {
            return false;
        }

        return (bool) $cart->items()->delete();
    }

    public function changeOrderStatus($invoiceId, $status)
    {
        $transaction = Transaction::where('invoice_id', $invoiceId)->first();
        if (!$transaction) {
            return false;
        }
        $order = $transaction->order;
        if (!$order->update(['status' => $status])) {
            return false;
        }

        if ($status === 'completed') {
            $this->recordCouponUsage($order);
        }

        return $order;
    }

    private function recordCouponUsage($order): void
    {
        if (!$order->coupon) {
            return;
        }

        $coupon = Coupon::where('code', $order->coupon)->first();
        if (!$coupon) {
            return;
        }

        CouponUsage::updateOrCreate(
            [
                'coupon_id' => $coupon->id,
                'user_id' => $order->user_id,
            ],
            [
                'order_id' => $order->id,
                'used_at' => now(),
            ]
        );
    }

    // public function getOrder($transactionId)
    // {
    //     $transaction = Transaction::where('invoice_id', $transactionId)->first();
    //     if (!$transaction) {
    //         return false;
    //     }
    //     return $order = $transaction->order;

    // }

    // public function sendAdminNotification($order)
    // {
    //     $admins = Admin::active()->whereHas('role', function ($q) {
    //         $q->whereJsonContains('permissions', 'notification');
    //     })->get();
    //     Notification::send($admins, new CreateOrderNotification($order));
    // }
}
