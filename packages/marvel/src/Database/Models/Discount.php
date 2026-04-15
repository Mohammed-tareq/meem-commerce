<?php

namespace Marvel\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Marvel\Enums\DiscountType;
class Discount extends Model
{


    protected $table = "discounts";

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceAfterDiscount(Product $product): float
    {
        $price = $product->price;

        if ($this->discount_type == DiscountType::FIXED_RATE) {
            $finalPrice = $price - $this->discount;
        } elseif ($this->discount_type == DiscountType::PERCENTAGE) {
            $finalPrice = $price - ($price * $this->discount / 100);
        } else {
            $finalPrice = $price;
        }

        $finalPrice = max(0, $finalPrice);

        return round($finalPrice, 2);
    }
}
