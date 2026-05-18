<?php

namespace App\Services\General\PromotionEngine\Strategies;

use App\Services\General\PromotionEngine\Contracts\PromotionStrategy;
use App\Services\General\PromotionEngine\PromotionResult;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Promotion;

class FixedPromotionStrategy extends AbstractPromotionStrategy implements PromotionStrategy
{
    public function apply(Promotion $promotion, Cart $cart, float $subtotal, int $matchedQuantity): PromotionResult
    {
        return new PromotionResult($promotion, $promotion->discountAmount($subtotal, $matchedQuantity));
    }
}
