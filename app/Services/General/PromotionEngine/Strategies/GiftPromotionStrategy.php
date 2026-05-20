<?php

declare(strict_types=1);

namespace App\Services\General\PromotionEngine\Strategies;

use App\Services\General\PromotionEngine\Contracts\PromotionStrategy;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Promotion;
use App\Services\General\PromotionEngine\PromotionEvaluation;
use App\Services\General\PromotionEngine\Outcome\GiftOutcome;
use App\Services\General\PromotionEngine\DTOs\GiftItem;
use App\Services\General\PromotionEngine\Outcome\PromotionOutcome;

class GiftPromotionStrategy extends AbstractPromotionStrategy implements PromotionStrategy
{
    public function eligible(Promotion $promotion, Cart $cart, int $subtotal, PromotionEvaluation $evaluation): bool
    {
        return parent::eligible($promotion, $cart, $subtotal, $evaluation)
            && $promotion->giftProducts->isNotEmpty();
    }

    public function computeOutcome(Promotion $promotion, Cart $cart, int $subtotal, PromotionEvaluation $evaluation): PromotionOutcome
    {
        $giftItems = $promotion->giftProducts
            ->filter(fn($product) => (int) ($product->available_stock ?? 0) > 0)
            ->map(function ($product) use ($promotion) {
                $quantity = max(1, (int) ($product->pivot->quantity ?? 1));
                return new GiftItem(
                    (int) $product->id,
                    (string) $product->name,
                    (string) $product->sku,
                    method_exists($product, 'getFirstMediaUrl') ? $product->getFirstMediaUrl('products') : null,
                    $quantity,
                    0,
                    true
                );
            })
            ->values()
            ->all();

        return new GiftOutcome($giftItems);
    }
}
