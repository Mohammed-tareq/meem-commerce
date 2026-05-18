<?php

namespace App\Services\General\PromotionEngine\Strategies;

use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Promotion;

abstract class AbstractPromotionStrategy
{
    public function eligible(Promotion $promotion, Cart $cart, float $subtotal, int $matchedQuantity): bool
    {
        if (!$promotion->isValid()) {
            return false;
        }

        if ($subtotal < (float) ($promotion->minimum_order_amount ?? 0)) {
            return false;
        }

        return $promotion->isRequiredQuantityTrue($matchedQuantity);
    }
}
