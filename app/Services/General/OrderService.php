<?php

namespace App\Services\General;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\CouponUsage;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\CartItem;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Transaction;
use Marvel\Enums\ShippingMethod;
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

    public function __construct(private PromotionService $promotionService) {}

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
            if (!$cart || $cart->items->isEmpty()) {
                return null;
            }
            $checkoutTotals = $this->calculateCheckoutTotals(
                $cart,
                (int) $request->input('selected_promotion_id') ?: null,
                (int) $request->input('selected_gift_product_id') ?: null,
            );
            $cart->update(['total_price' => $checkoutTotals['final_total']]);
            DB::commit();
            return $cart->total_price;
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return null;
        }
    }
    public function addItemsInOrder($request)
    {
        try {
            DB::beginTransaction();
            $cart = $this->getCartUser();
            if (!$cart || $cart->items->isEmpty()) {
                return null;
            }
            $checkoutTotals = $this->calculateCheckoutTotals(
                $cart,
                (int) $request->input('selected_promotion_id') ?: null,
                (int) $request->input('selected_gift_product_id') ?: null,
            );
            $order = $this->saveOrderInDatabase($request->only($this->dataArray), $cart, $checkoutTotals);
            if (!$order) {
                DB::rollBack();
                return null;
            }
            if (!$this->createOrderItems($order, $cart)) {
                DB::rollBack();
                return null;
            }
            $this->promotionService->incrementUsage($checkoutTotals['promotion']['id'] ?? null);
            DB::commit();
            return $order;
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return null;
        }
    }

    public function eligiblePromotionsForUser(): ?array
    {
        $cart = $this->getCartUser();
        if (!$cart || !$cart->items()->exists()) {
            return null;
        }

        return $this->promotionService->eligiblePromotionsPayload($cart);
    }

    protected function saveOrderInDatabase($order, $cart, array $checkoutTotals)
    {
        $couponData = $this->getCoupon($cart?->coupon ?? null);
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'name' => $order['name'],
            'user_phone' => $order['user_phone'],
            'user_email' => $order['user_email'],
            'address' => $order['address'],
            'notes' => $order['notes'] ?? null,
            'price' => $checkoutTotals['subtotal'],
            'shipping_price' => null,
            'total_price' => $checkoutTotals['final_total'],
            'coupon' => $couponData?->code ?? null,
            'coupon_discount' => $checkoutTotals['coupon_discount'] ?: null,
            'coupon_discount_type' => $couponData?->discount_type ?? null,
            'coupon_discount_max_amount' => $couponData?->max_discount_amount ?? null,
            'promotion_id' => $checkoutTotals['promotion']['id'] ?? null,
            'promotion_code' => $checkoutTotals['promotion']['code'] ?? null,
            'promotion_type' => $checkoutTotals['promotion']['type'] ?? null,
            'promotion_discount' => $checkoutTotals['promotion_discount'],
            'status' => 'pending',
        ]);
        return $order;
    }
    private function createOrderItems($order, $cart)
    {
        foreach ($cart->items as $item) {
            try {
                $quantity = max(1, (int) ($item->quantity ?? 0));
                $lineTotal = (float) ($item->total_price ?? 0);
                $effectiveUnitPrice = round($lineTotal / $quantity, 2);
                $promotionDiscountAmount = round(max(0, ((float) ($item->price ?? 0) * $quantity) - $lineTotal), 2);

                $product = $item->product ?? null;
                $productName = $product->name ?? 'No Name';
                $productSku = $product->sku ?? null;
                $shopName = null;
                if ($product && method_exists($product, 'shops')) {
                    $shopName = $product->shops()->first()?->name ?? null;
                }

                $flashSalePrice = null;
                if ($product && ($product->has_flash_sale ?? false)) {
                    $flashSale = $product->flash_sales()->valid()->where('id', $item->product->flash_sale_id)->first();
                    $flashSalePrice = $flashSale?->price ?? null;
                }

                $discountPrice = null;
                if ($product && ($product->has_discount ?? false)) {
                    $discountPrice = $product->discount_amount ?? null;
                }

                $orderItem = $order->orderItems()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $productName,
                    'product_quantity' => $quantity,
                    'product_price' => $effectiveUnitPrice,
                    'product_total_price' => round($lineTotal, 2),
                    'product_sku' => $productSku,
                    'shop_name' => $shopName,
                    'product_flash_sale_price' => $flashSalePrice,
                    'product_discount_price' => $discountPrice,
                    'promotion_discount_amount' => $promotionDiscountAmount,
                    'attributes' => $item->attributes ?? null,
                    'is_gift' => (bool) ($item->is_gift ?? false),
                    'promotion_id' => $item->promotion_id,
                ]);

                if (!$orderItem) {
                    return false;
                }
            } catch (\Exception $e) {
                report($e);
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
            ->with(['items' => fn($q) => $q->where('shipping_method', ShippingMethod::SCHEDULED), 'items.product', 'items.productVariant'])
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

    private function calculateCheckoutTotals(Cart $cart, ?int $selectedPromotionId, ?int $selectedGiftProductId = null): array
    {
        $promotionTotals = $this->promotionService->applySelectedPromotion($cart, $selectedPromotionId, $selectedGiftProductId);
        $priceAfterPromotion = $promotionTotals['final_total'];
        $priceAfterCoupon = $this->calculatePriceByCoupon($cart, $priceAfterPromotion);
        $finalTotal = round(max(0, (float) $priceAfterCoupon), 2);

        return [
            'subtotal' => $promotionTotals['subtotal'],
            'promotion_discount' => $promotionTotals['discount'],
            'coupon_discount' => round(max(0, (float) $priceAfterPromotion - (float) $finalTotal), 2),
            'final_total' => $finalTotal,
            'promotion' => $promotionTotals['promotion'],
            'gift_items' => $promotionTotals['gift_items'],
        ];
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
