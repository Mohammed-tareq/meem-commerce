<?php

namespace App\Services\General;

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
        private PromotionService $promotionService,
        private CartInventoryService $cartInventoryService,
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

            $finalTotal = round(max(0, (float) $checkoutTotals['final_total'] + $fastShippingFee), 2);

            $orderData = $request->only(['name', 'user_phone', 'user_email', 'address', 'notes']);
            $order = Order::create([
                'user_id' => $user->id,
                'name' => $orderData['name'],
                'user_phone' => $orderData['user_phone'],
                'user_email' => $orderData['user_email'],
                'address' => $orderData['address'],
                'notes' => $orderData['notes'] ?? null,
                'shipping_method' => ShippingMethod::FAST,
                'expected_delivery_at' => $eta,
                'fast_shipping_fee' => $fastShippingFee,
                'price' => $checkoutTotals['subtotal'],
                'shipping_price' => null,
                'total_price' => $finalTotal,
                'coupon' => $checkoutTotals['coupon'] ?? null,
                'coupon_discount' => $checkoutTotals['coupon_discount'] ?? null,
                'coupon_discount_type' => $checkoutTotals['coupon_discount_type'] ?? null,
                'coupon_discount_max_amount' => $checkoutTotals['coupon_discount_max_amount'] ?? null,
                'promotion_id' => $checkoutTotals['promotion']['id'] ?? null,
                'promotion_code' => $checkoutTotals['promotion']['code'] ?? null,
                'promotion_type' => $checkoutTotals['promotion']['type'] ?? null,
                'promotion_discount' => $checkoutTotals['promotion_discount'],
                'status' => 'pending',
            ]);

            if (!$order) {
                DB::rollBack();
                throw new Exception('Failed to create order.');
            }

            if (!$this->createOrderItems($order, $cart)) {
                DB::rollBack();
                throw new Exception('Failed to add items to order.');
            }

            $this->promotionService->incrementUsage($checkoutTotals['promotion']['id'] ?? null);

            $cart->update(['total_price' => $finalTotal]);

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

    private function calculateCheckoutTotals(Cart $cart, Request $request): array
    {
        $selectedPromotionId = (int) $request->input('selected_promotion_id') ?: null;
        $selectedGiftProductId = (int) $request->input('selected_gift_product_id') ?: null;

        $promotionTotals = $this->promotionService->applySelectedPromotion(
            $cart,
            $selectedPromotionId,
            $selectedGiftProductId
        );

        $priceAfterPromotion = $promotionTotals['final_total'];
        $couponData = $this->getCouponData($cart);
        $couponDiscount = $couponData ? $this->calculateCouponDiscount($couponData, $priceAfterPromotion) : 0;
        $finalTotal = round(max(0, (float) $priceAfterPromotion - $couponDiscount), 2);

        return [
            'subtotal' => $promotionTotals['subtotal'],
            'promotion_discount' => $promotionTotals['discount'],
            'coupon_discount' => $couponDiscount,
            'final_total' => $finalTotal,
            'promotion' => $promotionTotals['promotion'],
            'gift_items' => $promotionTotals['gift_items'],
            'coupon' => $cart->coupon,
            'coupon_discount_type' => $couponData?->discount_type ?? null,
            'coupon_discount_max_amount' => $couponData?->max_discount_amount ?? null,
        ];
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

    private function createOrderItems(Order $order, Cart $cart): bool
    {
        foreach ($cart->items as $item) {
            try {
                $quantity = max(1, (int) ($item->quantity ?? 0));
                $lineTotal = (float) ($item->total_price ?? 0);
                $effectiveUnitPrice = $quantity > 0 ? round($lineTotal / $quantity, 2) : 0;
                $promotionDiscountAmount = round(max(0, ((float) ($item->price ?? 0) * $quantity) - $lineTotal), 2);

                $product = $item->product;
                $productName = $product?->name ?? 'No Name';
                $productSku = $product?->sku ?? null;
                $shopName = null;
                if ($product && method_exists($product, 'shops')) {
                    $shopName = $product->shops()->first()?->name ?? null;
                }

                $flashSalePrice = null;
                if ($product && ($product->has_flash_sale ?? false)) {
                    $flashSale = $product->flash_sales()->valid()->where('id', $product->flash_sale_id)->first();
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
            } catch (Exception $e) {
                report($e);
                return false;
            }
        }

        return true;
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
