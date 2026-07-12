<?php

namespace App\Services\General;

use App\DTOs\CheckoutTotals;
use App\Events\OrderCreated;
use App\Services\Checkout\OrderCreationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Governorate;
use Marvel\Database\Models\Order;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Transaction;
use Marvel\Database\Repositories\FastShippingRepository;
use Marvel\Enums\ShippingMethod;

class FastShippingService
{
    public function __construct(
        private FastShippingRepository $fastShippingRepo,
        private OrderService $orderService,
        private PromotionService $promotionService,
        private CartInventoryService $cartInventoryService,
        private OrderCreationService $orderCreationService,
    ) {}

    public function getStatus(): array
    {
        return $this->fastShippingRepo->getStatus();
    }

    public function getFastShippingProducts(Request $request): LengthAwarePaginator
    {
        $limit = $this->getLimit($request);
        $term = trim((string) $request->get('search', ''));

        $query = Product::query()
            ->active()
            ->fastShippingAvailable()
            ->with(['categories', 'variations'])
            ->withAvg(['reviews' => fn($q) => $q->approved()], 'rating')
            ->withCount(['reviews' => fn($q) => $q->approved()]);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        return $query->orderByDesc('id')->paginate($limit);
    }

    public function createFastOrder(Request $request): Order
    {
        $user = $request->user();
        $cart = $this->cartInventoryService->getActiveCartForUser($user);

        if (!$cart || !$cart->items()->exists()) {
            throw new \InvalidArgumentException('Cart is empty.');
        }

        $governorate = Governorate::query()->find($request->input('governorate_id'));
        if (!$governorate) {
            throw new \InvalidArgumentException('Governorate not found.');
        }

        $cart->load(['items' => fn($q) => $q->where('shipping_method', ShippingMethod::FAST), 'items.product', 'items.productVariant']);

        if ($cart->items->isEmpty()) {
            throw new \InvalidArgumentException('No fast shipping items in cart.');
        }

        $errors = $this->fastShippingRepo->validateCheckout($governorate, $cart->items);

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(' ', $errors));
        }

        try {
            DB::beginTransaction();

            $checkoutTotals = $this->calculateCheckoutTotals($cart, $request);
            $fastShippingFee = $this->fastShippingRepo->getFee();
            $eta = $this->fastShippingRepo->calculateEta();

            $governorateId = (int) $request->input('governorate_id');
            $shippingInfo = $this->orderService->getGovernorateShippingInfo($governorateId);
            $shippingPrice = $shippingInfo['price'];
            if ($shippingInfo['free_shipping_over'] !== null && $checkoutTotals->subtotal > $shippingInfo['free_shipping_over']) {
                $shippingPrice = 0;
            }

            $orderData = $request->only(['name', 'user_phone', 'user_email', 'address', 'notes',
                'fulfillment_type', 'payment_method', 'payment_gateway', 'pickup_location_id',
            ]);
            $orderData['user_id'] = $user->id;

            $order = $this->orderCreationService->createOrder(
                $orderData,
                $cart,
                $checkoutTotals,
                ShippingMethod::FAST,
                $eta,
                $fastShippingFee,
                $shippingPrice,
                $governorateId,
            );

            if (!$order) {
                DB::rollBack();
                throw new Exception('Failed to create order.');
            }

            if (!$this->orderCreationService->createOrderItems($order, $cart)) {
                DB::rollBack();
                throw new Exception('Failed to add items to order.');
            }

            $this->orderCreationService->finalizeOrder($order, $checkoutTotals);

            $cart->update(['total_price' => $order->total_price]);

            DB::commit();

            return $order->load(['orderItems.product', 'orderItems.productVariant']);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function paginateFastOrders(Request $request): LengthAwarePaginator
    {
        $limit = $this->getLimit($request);
        $userId = (int) $request->user()->id;

        return Order::query()
            ->fast()
            ->forUser($userId)
            ->with(['orderItems.product.media', 'orderItems.productVariant.attributeProducts.attributeValue'])
            ->paginate($limit)
            ->withQueryString();
    }

    private function calculateCheckoutTotals(Cart $cart, Request $request): CheckoutTotals
    {
        $selectedPromotionId = (int) $request->input('selected_promotion_id') ?: null;
        $selectedGiftProductId = (int) $request->input('selected_gift_product_id') ?: null;

        $promotionTotals = $this->promotionService->applySelectedPromotion(
            $cart,
            $selectedPromotionId,
            $selectedGiftProductId
        );

        $priceAfterPromotion = $promotionTotals->finalTotal;
        $couponData = $this->getCouponData($cart);
        $couponDiscount = $couponData ? $this->calculateCouponDiscount($couponData, $priceAfterPromotion) : 0;
        $finalTotal = round(max(0, (float) $priceAfterPromotion - $couponDiscount), 2);

        return new CheckoutTotals(
            subtotal: $promotionTotals->subtotal,
            promotionDiscount: $promotionTotals->promotionDiscount,
            couponDiscount: $couponDiscount,
            finalTotal: $finalTotal,
            promotion: $promotionTotals->promotion,
            giftItems: $promotionTotals->giftItems,
            coupon: $cart->coupon,
            couponDiscountType: $couponData?->discount_type ?? null,
            couponDiscountMaxAmount: $couponData?->max_discount_amount ?? null,
        );
    }

    private function getCouponData(?Cart $cart): ?\Marvel\Database\Models\Coupon
    {
        if (!$cart || !$cart->coupon) {
            return null;
        }

        return \Marvel\Database\Models\Coupon::valid()->where('code', $cart->coupon)->first();
    }

    private function calculateCouponDiscount($coupon, float $price): float
    {
        return app(\Marvel\Services\Pricing\ProductPricingService::class)
            ->calculateCouponPrice($coupon, $price);
    }

    private function getLimit(Request $request): int
    {
        $limit = (int) $request->get('limit', 15);

        if ($limit <= 0) {
            return 15;
        }

        return min($limit, 100);
    }
}
