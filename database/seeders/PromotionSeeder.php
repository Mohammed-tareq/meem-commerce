<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\ProductVariant;
use Marvel\Enums\PromotionMountType;
use Marvel\Enums\PromotionType;

class PromotionSeeder extends Seeder
{
    private const PROMOTION_COUNT = 60;

    public function run(): void
    {
        $productIds = Product::query()->pluck('id')->all();
        $promotionImages = File::exists(public_path('images/banners'))
            ? collect(File::files(public_path('images/banners')))
            : collect();

        for ($index = 1; $index <= self::PROMOTION_COUNT; $index++) {
            $promotion = match ($index % 3) {
                0 => $this->createGiftPromotion($index, $productIds),
                1 => $this->createPercentagePromotion($index, $productIds),
                default => $this->createFixedPromotion($index, $productIds),
            };

            $this->attachPromotionImage($promotion, $promotionImages, $index);
        }
    }

    private function createPercentagePromotion(int $index, array $productIds): Promotion
    {
        $requiredProductIds = $this->randomProductIds($productIds, rand(0, 1) === 1 ? rand(1, 3) : 0);
        $discount = collect([5, 10, 15, 20, 25, 30])->random();

        $promotion = $this->createPromotion([
            'name' => [
                'en' => "{$discount}% OFF Promotion {$index}",
                'ar' => "خصم {$discount}% {$index}",
            ],
            'type' => PromotionType::PRICE,
            'type_amount' => PromotionMountType::PERCENTAGE,
            'value' => $discount,
            'discount' => $discount,
            'max_discount_amount' => collect([50, 75, 100, 150, 200])->random(),
            'required_quantity_type' => rand(1, 3),
            'minimum_order_amount' => collect([0, 250, 500, 750, 1000])->random(),
            'apply_to' => empty($requiredProductIds) ? 'all_products' : 'specific_products',
        ]);

        $promotion->products()->sync($requiredProductIds);

        return $promotion;
    }

    private function createFixedPromotion(int $index, array $productIds): Promotion
    {
        $requiredProductIds = $this->randomProductIds($productIds, rand(0, 1) === 1 ? rand(1, 3) : 0);
        $discount = collect([25, 50, 75, 100, 150, 200])->random();

        $promotion = $this->createPromotion([
            'name' => [
                'en' => "{$discount} EGP OFF Promotion {$index}",
                'ar' => "خصم {$discount} جنيه {$index}",
            ],
            'type' => PromotionType::PRICE,
            'type_amount' => PromotionMountType::FIXED_RATE,
            'value' => $discount,
            'discount' => $discount,
            'max_discount_amount' => null,
            'required_quantity_type' => rand(1, 3),
            'minimum_order_amount' => collect([0, 300, 600, 900])->random(),
            'apply_to' => empty($requiredProductIds) ? 'all_products' : 'specific_products',
        ]);

        $promotion->products()->sync($requiredProductIds);

        return $promotion;
    }

    private function createGiftPromotion(int $index, array $productIds): Promotion
    {
        $requiredProductIds = $this->randomProductIds($productIds, rand(1, 3));
        $giftProductIds = $this->randomProductIds($productIds, rand(1, 2), $requiredProductIds);

        if (empty($giftProductIds) && !empty($productIds)) {
            $giftProductIds = $this->randomProductIds($productIds, 1);
        }

        $promotion = $this->createPromotion([
            'name' => [
                'en' => "Buy More Get Gift {$index}",
                'ar' => "اشتري أكثر واحصل على هدية {$index}",
            ],
            'type' => PromotionType::QTY,
            'type_amount' => PromotionMountType::GIFT,
            'value' => 0,
            'discount' => 0,
            'max_discount_amount' => null,
            'required_quantity_type' => rand(2, 5),
            'minimum_order_amount' => collect([0, 250, 500])->random(),
            'apply_to' => empty($requiredProductIds) ? 'all_products' : 'specific_products',
        ]);

        $promotion->products()->sync($requiredProductIds);

        $giftProducts = [];

        foreach ($giftProductIds as $productId) {
            $variant = ProductVariant::where('product_id', $productId)->first();

            if (!$variant) {
                $variant = ProductVariant::inRandomOrder()->first();
            }

            if (!$variant) {
                continue;
            }

            $giftProducts[(int) $productId] = [
                'quantity' => rand(1, 2),
                'product_variant_id' => $variant->id,
            ];
        }

        // If no suitable variants found for the selected gifts, fall back to any available variant.
        if (empty($giftProducts)) {
            $fallback = ProductVariant::inRandomOrder()->first();
            if ($fallback) {
                $giftProducts[(int) $fallback->product_id] = ['quantity' => 1, 'product_variant_id' => $fallback->id];
            }
        }

        $promotion->giftProducts()->sync($giftProducts);

        return $promotion;
    }

    private function createPromotion(array $attributes): Promotion
    {
        $applyTo = $attributes['apply_to'] ?? 'specific_products';

        return Promotion::create(array_merge([
            'code' => $this->generatePromotionCode($applyTo),
            'limiter' => rand(25, 250),
            'usage' => 0,
            'start_at' => Carbon::now()->subDays(rand(0, 10)),
            'end_at' => Carbon::now()->addDays(rand(10, 60)),
            'status' => true,
        ], $attributes));
    }

    private function generatePromotionCode(string $applyTo, int $length = 10): string
    {
        $prefix = match ($applyTo) {
            'all_products' => 'ALL',
            'specific_products' => 'PRO',
            'specific_categories' => 'CAT',
            default => 'PRO',
        };

        return $prefix . strtoupper(Str::random($length));
    }

    private function randomProductIds(array $productIds, int $count, array $exclude = []): array
    {
        $availableProductIds = array_values(array_diff($productIds, $exclude));

        if ($count <= 0 || empty($availableProductIds)) {
            return [];
        }

        return collect($availableProductIds)
            ->shuffle()
            ->take(min($count, count($availableProductIds)))
            ->values()
            ->all();
    }

    private function attachPromotionImage(Promotion $promotion, $promotionImages, int $index): void
    {
        try {
            $imagesToAttach = 9;

            if ($promotionImages->isNotEmpty()) {
                $total = $promotionImages->count();
                for ($i = 0; $i < $imagesToAttach; $i++) {
                    $image = $promotionImages[($index - 1 + $i) % $total];
                    $collection = $i % 2 === 0 ? 'promotions-desktop' : 'promotions-mobile';
                    $promotion->addMedia($image->getPathname())
                        ->preservingOriginal()
                        ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                        ->toMediaCollection($collection, 'promotions');
                }

                return;
            }

            // Fallback to picsum seeds so each promotion gets multiple images
            for ($i = 0; $i < $imagesToAttach; $i++) {
                $seed = $index . '-' . $i;
                $collection = $i % 2 === 0 ? 'promotions-desktop' : 'promotions-mobile';
                $promotion->addMediaFromUrl('https://picsum.photos/seed/promotion' . $seed . '/1200/400')
                    ->toMediaCollection($collection, 'promotions');
            }
        } catch (\Exception $e) {
            // Image attachment should not block demo data creation.
        }
    }
}
