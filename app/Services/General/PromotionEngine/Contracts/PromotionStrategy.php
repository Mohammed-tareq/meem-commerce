<?php

namespace App\Services\General\PromotionEngine\Contracts;

use App\Services\General\PromotionEngine\PromotionResult;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Promotion;

interface PromotionStrategy
{
    public function eligible(Promotion $promotion, Cart $cart, float $subtotal, int $matchedQuantity): bool;

    public function apply(Promotion $promotion, Cart $cart, float $subtotal, int $matchedQuantity): PromotionResult;
}
