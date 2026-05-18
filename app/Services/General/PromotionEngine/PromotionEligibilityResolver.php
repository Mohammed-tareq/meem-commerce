<?php

namespace App\Services\General\PromotionEngine;

use App\Services\General\PromotionEngine\Contracts\PromotionStrategy;
use App\Services\General\PromotionEngine\Strategies\FixedPromotionStrategy;
use App\Services\General\PromotionEngine\Strategies\GiftPromotionStrategy;
use App\Services\General\PromotionEngine\Strategies\PercentagePromotionStrategy;
use Illuminate\Support\Collection;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Promotion;
use Marvel\Enums\PromotionMountType;

class PromotionEligibilityResolver
{
    /** @var array<string, PromotionStrategy> */
    private array $strategies;

    public function __construct()
    {
        $this->strategies = [
            PromotionMountType::PERCENTAGE => app(PercentagePromotionStrategy::class),
            PromotionMountType::FIXED_RATE => app(FixedPromotionStrategy::class),
            PromotionMountType::GIFT => app(GiftPromotionStrategy::class),
        ];
    }

    public function eligible(Cart $cart, Collection $promotions, float $subtotal): Collection
    {
        return $promotions
            ->map(fn (Promotion $promotion) => $this->resolve($cart, $promotion, $subtotal))
            ->filter()
            ->values();
    }

    public function resolve(Cart $cart, Promotion $promotion, float $subtotal): ?PromotionResult
    {
        $strategy = $this->strategies[$promotion->type_amount] ?? null;

        if (!$strategy) {
            return null;
        }

        if (!$promotion->appliesToAllProducts() && $promotion->products->isEmpty()) {
            return null;
        }

        $matchedQuantity = $this->matchedQuantity($cart, $promotion);

        if (!$strategy->eligible($promotion, $cart, $subtotal, $matchedQuantity)) {
            return null;
        }

        return $strategy->apply($promotion, $cart, $subtotal, $matchedQuantity);
    }

    private function matchedQuantity(Cart $cart, Promotion $promotion): int
    {
        $requiredProductIds = $promotion->products->pluck('id')->map(fn ($id) => (int) $id)->all();

        return $cart->items
            ->filter(function ($item) use ($promotion, $requiredProductIds) {
                if ((bool) ($item->is_gift ?? false)) {
                    return false;
                }

                if ($promotion->appliesToAllProducts()) {
                    return true;
                }

                return in_array((int) $item->product_id, $requiredProductIds, true);
            })
            ->sum(fn ($item) => (int) $item->quantity);
    }
}
