<?php

namespace App\Services\General\PromotionEngine\Strategies;

use App\Services\General\PromotionEngine\Contracts\PromotionStrategy;
use App\Services\General\PromotionEngine\PromotionResult;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Promotion;

class GiftPromotionStrategy extends AbstractPromotionStrategy implements PromotionStrategy
{
    public function eligible(Promotion $promotion, Cart $cart, float $subtotal, int $matchedQuantity): bool
    {
        return parent::eligible($promotion, $cart, $subtotal, $matchedQuantity)
            && $promotion->giftProducts->isNotEmpty();
    }

    public function apply(Promotion $promotion, Cart $cart, float $subtotal, int $matchedQuantity): PromotionResult
    {
        $giftItems = $promotion->giftProducts->map(function ($product) use ($promotion) {
            return [
                'promotion_id' => $promotion->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'product_image' => method_exists($product, 'getFirstMediaUrl') ? $product->getFirstMediaUrl('products') : null,
                'quantity' => max(1, (int) ($product->pivot->quantity ?? 1)),
                'price' => 0.0,
                'is_gift' => true,
            ];
        })->values()->all();

        return new PromotionResult($promotion, 0.0, $giftItems);
    }
}
