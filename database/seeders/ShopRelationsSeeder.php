<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\Shop;

class ShopRelationsSeeder extends Seeder
{
    public function run(): void
    {
        $shops = Shop::all();

        $categoryIds = Category::pluck('id')->all();
        $productIds = Product::pluck('id')->all();
        $couponIds = Coupon::pluck('id')->all();
        $promotionIds = Promotion::pluck('id')->all();
        $flashSaleIds = FlashSale::pluck('id')->all();

        foreach ($shops as $shop) {
            $this->syncRandom($shop->categories(), $categoryIds, 2, 6);
            $this->syncRandom($shop->products(), $productIds, 3, 12);
            $this->syncRandom($shop->coupons(), $couponIds, 1, 5);
            $this->syncRandom($shop->promotions(), $promotionIds, 1, 5);
            $this->syncRandom($shop->flashSales(), $flashSaleIds, 1, 3);
        }
    }

    private function syncRandom($relation, array $ids, int $min, int $max): void
    {
        if (empty($ids)) {
            return;
        }

        $max = min($max, count($ids));
        $min = min($min, $max);
        $count = $min === $max ? $max : rand($min, $max);

        $selected = $this->pickRandomIds($ids, $count);
        $relation->syncWithoutDetaching($selected);
    }

    private function pickRandomIds(array $ids, int $count): array
    {
        if ($count <= 0) {
            return [];
        }

        if ($count >= count($ids)) {
            return $ids;
        }

        $selected = array_rand($ids, $count);

        if (!is_array($selected)) {
            return [$ids[$selected]];
        }

        $values = [];
        foreach ($selected as $index) {
            $values[] = $ids[$index];
        }

        return $values;
    }
}
