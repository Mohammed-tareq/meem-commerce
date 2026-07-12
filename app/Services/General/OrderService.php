<?php

namespace App\Services\General;

use App\DTOs\CheckoutTotals;
use App\Events\OrderCreated;
use App\Services\Checkout\OrderCreationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\CouponUsage;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\CartItem;
use Marvel\Database\Models\Governorate;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\ShippingPrice;
use Marvel\Database\Models\Transaction;
use Marvel\Enums\ShippingMethod;
use App\Events\OrderCancelled;
use App\Events\OrderStatusChanged;
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
        'governorate_id',
    ];

    public function __construct(
        private PromotionService $promotionService,
        private OrderCreationService $orderCreationService,
    ) {}

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
            'orderItems.product.media',
            'orderItems.productVariant.attributeProducts.attributeValue',
            'transactions',
            'pickupLocation',
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
            if (!$cart) {
                DB::rollBack();
                throw new \InvalidArgumentException(__('checkout.cart_not_found')); 
            }
            if ($cart->items->isEmpty()) {
                DB::rollBack();
                throw new \InvalidArgumentException(__('checkout.cart_empty'));
            }
            $checkoutTotals = $this->calculateCheckoutTotals(
                $cart,
                (int) $request->input('selected_promotion_id') ?: null,
                (int) $request->input('selected_gift_product_id') ?: null,
            );

            $shippingInfo = $this->resolveShippingPrice((int) $request->input('governorate_id') ?: null);
            $shippingPrice = $shippingInfo['price'];
            if ($shippingInfo['free_shipping_over'] !== null && $checkoutTotals->subtotal > $shippingInfo['free_shipping_over']) {
                $shippingPrice = 0;
            }

            $finalTotal = round((float) $checkoutTotals->finalTotal + $shippingPrice, 2);
            $cart->update(['total_price' => $finalTotal]);
            DB::commit();
            return $cart->total_price;
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw new \InvalidArgumentException($e->getMessage(), 0, $e);
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
            $checkoutTotals = $this->getCheckoutTotalsFromCart($cart);

            $orderData = $request->only(array_merge($this->dataArray, [
                'fulfillment_type', 'payment_method', 'payment_gateway', 'pickup_location_id',
            ]));
            $orderData['user_id'] = $request->user()->id;

            $shippingInfo = $this->resolveShippingPrice((int) ($orderData['governorate_id'] ?? null));
            $shippingPrice = $shippingInfo['price'];
            if ($shippingInfo['free_shipping_over'] !== null && $checkoutTotals->subtotal > $shippingInfo['free_shipping_over']) {
                $shippingPrice = 0;
            }
            $governorateId = $shippingInfo['governorate_id'];

            $order = $this->orderCreationService->createOrder(
                $orderData, $cart, $checkoutTotals, null, null, null, $shippingPrice, $governorateId,
            );
            if (!$order) {
                DB::rollBack();
                return null;
            }
            if (!$this->orderCreationService->createOrderItems($order, $cart)) {
                DB::rollBack();
                return null;
            }
            $this->orderCreationService->finalizeOrder($order, $checkoutTotals);
            DB::commit();

            return $order->load(['orderItems.product', 'orderItems.productVariant']);
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
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



    public function getGovernorateShippingInfo(?int $governorateId): array
    {
        return $this->resolveShippingPrice($governorateId);
    }

    private function resolveShippingPrice(?int $governorateId): array
    {
        if (!$governorateId) {
            return ['price' => 0, 'free_shipping_over' => null, 'governorate_id' => null];
        }

        $governorate = Governorate::query()->where('id', $governorateId)->where('status', true)->first();
        if (!$governorate) {
            return ['price' => 0, 'free_shipping_over' => null, 'governorate_id' => null];
        }

        $shippingPrice = $governorate->shippingPrice()
            ->where('status', true)
            ->first();

        if (!$shippingPrice) {
            return ['price' => 0, 'free_shipping_over' => null, 'governorate_id' => $governorateId];
        }

        return [
            'price' => (float) $shippingPrice->price,
            'free_shipping_over' => $shippingPrice->free_shipping_over !== null ? (float) $shippingPrice->free_shipping_over : null,
            'governorate_id' => $governorateId,
        ];
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

    private function getCheckoutTotalsFromCart(Cart $cart): CheckoutTotals
    {
        $items = $cart->items->reject(fn($item) => (bool) ($item->is_gift ?? false));

        $subtotal = round((float) $items->sum(function ($item) {
            $baseLineTotal = ((float) ($item->price ?? 0)) * ((int) ($item->quantity ?? 0));
            if ($baseLineTotal > 0) {
                return $baseLineTotal;
            }
            return (float) ($item->total_price ?? 0);
        }), 2);

        $promotionDiscount = round((float) $items->sum(fn($item) => (float) ($item->discount_amount ?? 0)), 2);
        $finalTotal = round((float) $items->sum('total_price'), 2);

        $promotionItem = $items->first(fn($item) => !is_null($item->promotion_id));
        $promotionData = null;
        if ($promotionItem) {
            $promotion = Promotion::query()->find((int) $promotionItem->promotion_id);
            $promotionData = $promotion ? [
                'id' => (int) $promotion->id,
                'type' => $promotion->type_amount,
                'code' => $promotion->code,
            ] : null;
        }

        return new CheckoutTotals(
            subtotal: $subtotal,
            promotionDiscount: $promotionDiscount,
            couponDiscount: round(max(0, $subtotal - $promotionDiscount - $finalTotal), 2),
            finalTotal: $finalTotal,
            promotion: $promotionData,
            giftItems: [],
        );
    }

    private function calculateCheckoutTotals(Cart $cart, ?int $selectedPromotionId, ?int $selectedGiftProductId = null): CheckoutTotals
    {
        $promotionTotals = $this->promotionService->applySelectedPromotion($cart, $selectedPromotionId, $selectedGiftProductId);
        $priceAfterPromotion = $promotionTotals->finalTotal;
        $priceAfterCoupon = $this->calculatePriceByCoupon($cart, $priceAfterPromotion);
        $finalTotal = round(max(0, (float) $priceAfterCoupon), 2);

        return new CheckoutTotals(
            subtotal: $promotionTotals->subtotal,
            promotionDiscount: $promotionTotals->promotionDiscount,
            couponDiscount: round(max(0, (float) $priceAfterPromotion - (float) $finalTotal), 2),
            finalTotal: $finalTotal,
            promotion: $promotionTotals->promotion,
            giftItems: $promotionTotals->giftItems,
        );
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

    public function changeOrderStatus($invoiceId, $status, $orderId = null)
    {
        $order = null;
        $transaction = null;

        if ($invoiceId) {
            $transaction = Transaction::where('invoice_id', $invoiceId)->first();
            if ($transaction) {
                $order = $transaction->order;
            }
        }

        if (!$order && $orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $transaction = $order->transactions()->latest()->first();
            }
        }

        if (!$order) {
            return false;
        }

        $previousStatus = $order->status;

        if (!$order->update(['status' => $status])) {
            return false;
        }

        if ($status === 'completed') {
            $this->recordCouponUsage($order);
        }

        if ($transaction) {
            if ($status === 'completed') {
                $transaction->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            if ($status === 'cancelled') {
                $transaction->update([
                    'status' => 'failed',
                ]);
            }
        }

        event(new OrderStatusChanged($order));

        if ($status === 'cancelled' && $previousStatus === 'completed') {
            event(new OrderCancelled($order));
        }

        return $order;
    }

    public function markCodAsPaid(Order $order): void
    {
        $transaction = $order->transactions()
            ->where('payment_method', 'cod')
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$transaction) {
            throw new \RuntimeException('No pending COD transaction found.');
        }

        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $order->update(['status' => 'completed']);

        $this->recordCouponUsage($order);

        event(new \App\Events\PaymentSucceeded($order));
    }

    public function markCashierPaid(Order $order): void
    {
        $transaction = $order->transactions()
            ->where('payment_method', 'pay_at_cashier')
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$transaction) {
            throw new \RuntimeException('No pending Pay at Cashier transaction found.');
        }

        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $order->update(['status' => 'completed']);

        $this->recordCouponUsage($order);

        event(new \App\Events\PaymentSucceeded($order));
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
}