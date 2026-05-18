<?php

namespace App\Services\General;

use App\Services\General\PromotionEngine\PromotionEligibilityResolver;
use App\Services\General\PromotionEngine\PromotionResult;
use Illuminate\Support\Collection;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Promotion;

class PromotionService
{
    public function __construct(
        private PromotionEligibilityResolver $resolver,
        private CartInventoryService $inventoryService,
    ) {
    }

    public function eligiblePromotions(Cart $cart): Collection
    {
        $cart->load(['items.product', 'items.productVariant']);

        $subtotal = $this->subtotal($cart);
        $promotions = Promotion::valid()
            ->with(['products:id', 'giftProducts:id,name,sku'])
            ->get();

        return $this->resolver->eligible($cart, $promotions, $subtotal);
    }

    public function eligiblePromotionsPayload(Cart $cart): array
    {
        return [
            'eligible_promotions' => $this->eligiblePromotions($cart)
                ->map(fn (PromotionResult $result) => $result->toArray())
                ->values()
                ->all(),
        ];
    }

    public function applySelectedPromotion(Cart $cart, ?int $promotionId): array
    {
        $this->removeGiftItems($cart);
        $cart->load(['items.product', 'items.productVariant']);

        $subtotal = $this->subtotal($cart);
        $result = null;

        if ($promotionId) {
            $promotion = Promotion::valid()
                ->whereKey($promotionId)
                ->with(['products:id', 'giftProducts:id,name,sku'])
                ->lockForUpdate()
                ->first();

            if (!$promotion) {
                throw new \InvalidArgumentException('Selected promotion is not valid.');
            }

            $result = $this->resolver->resolve($cart, $promotion, $subtotal);

            if (!$result) {
                throw new \InvalidArgumentException('Selected promotion is not eligible for this cart.');
            }

            $this->applyGiftItems($cart, $result);
        }

        $discount = min($subtotal, (float) ($result?->discount ?? 0));
        $finalTotal = round(max(0, $subtotal - $discount), 2);

        $cart->forceFill(['total_price' => $finalTotal])->save();

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'final_total' => $finalTotal,
            'promotion' => $result ? [
                'id' => $result->promotion->id,
                'type' => $result->promotion->type_amount,
                'code' => $result->promotion->code,
            ] : null,
            'gift_items' => $result?->giftItems ?? [],
        ];
    }

    public function incrementUsage(?int $promotionId): void
    {
        if (!$promotionId) {
            return;
        }

        Promotion::query()
            ->whereKey($promotionId)
            ->where(function ($query) {
                $query->whereNull('limiter')
                    ->orWhereColumn('usage', '<', 'limiter');
            })
            ->lockForUpdate()
            ->first()
            ?->increment('usage');
    }

    private function removeGiftItems(Cart $cart): void
    {
        $cart->items()
            ->where('is_gift', true)
            ->get()
            ->each(fn ($item) => $this->inventoryService->releaseItem($item, true));
    }

    private function applyGiftItems(Cart $cart, PromotionResult $result): void
    {
        foreach ($result->giftItems as $giftItem) {
            $product = Product::query()
                ->whereKey($giftItem['product_id'])
                ->lockForUpdate()
                ->first();

            if (!$product) {
                continue;
            }

            $this->inventoryService->reserveGiftItem($cart, $product, $result->promotion, max(1, (int) $giftItem['quantity']));
        }
    }

    private function subtotal(Cart $cart): float
    {
        return round((float) $cart->items
            ->reject(fn ($item) => (bool) ($item->is_gift ?? false))
            ->sum('total_price'), 2);
    }
}
