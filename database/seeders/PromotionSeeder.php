<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\Product;
use Marvel\Enums\PromotionType;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $productIds = Product::query()->pluck('id')->all();
        $promotionCount = 100;

        $promotionImages = collect(File::files(public_path('images/banners')));
        $promotionImagesCount = $promotionImages->count();

        for ($index = 1; $index <= $promotionCount; $index++) {
            $type = rand(0, 1) === 0 ? PromotionType::PRICE : PromotionType::QTY;
            $typeAmount = $type === PromotionType::QTY
                ? 'gift'
                : (rand(0, 1) === 0 ? 'percentage' : 'fixed_rate');
            $isGiftPromotion = $typeAmount === 'gift';
            $promotionProductIds = [];

            if ($isGiftPromotion && !empty($productIds)) {
                $sampleSize = min(count($productIds), rand(1, 3));
                $randomKeys = array_rand(array_flip($productIds), $sampleSize);
                $promotionProductIds = is_array($randomKeys) ? array_values($randomKeys) : [$randomKeys];
            }

            $value = $typeAmount === 'percentage'
                ? rand(5, 50)
                : rand(5, 200);

            $requiredQuantity = $type === PromotionType::QTY
                ? rand(2, 5)
                : rand(1, 3);

            $promotion = Promotion::create([
                'name' => [
                    'en' => $type === PromotionType::QTY
                        ? "Quantity Promotion {$index}"
                        : "Promotion {$index}",
                    'ar' => $type === PromotionType::QTY
                        ? "عرض حسب الكمية {$index}"
                        : "عرض ترويجي {$index}",
                ],
                'type' => $type,
                'type_amount' => $typeAmount,
                'value' => $value,
                'max_discount_amount' => $typeAmount === 'percentage' ? rand(20, 100) : null,
                'code' => (string) Str::uuid(),
                'required_quantity_type' => $requiredQuantity,
                'limiter' => rand(10, 100),
                'usage' => rand(0, 20),
                'start_at' => Carbon::now()->subDays(rand(0, 5)),
                'end_at' => Carbon::now()->addDays(rand(5, 30)),
                'status' => true,
            ]);

            if (!empty($promotionProductIds)) {
                $promotion->products()->sync($promotionProductIds);
            }

            // attach a banner image from local `public/images/banners` if available,
            // otherwise fall back to a remote placeholder.
            try {
                if ($promotionImagesCount > 0) {
                    $image = $promotionImages[($index - 1) % $promotionImagesCount];
                    $promotion->addMedia($image->getPathname())
                        ->preservingOriginal()
                        ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                        ->toMediaCollection('promotions', 'promotions');
                } else {
                    $imageUrl = 'https://picsum.photos/seed/promotion' . $index . '/1200/400';
                    $promotion->addMediaFromUrl($imageUrl)->toMediaCollection('promotions', 'promotions');
                }
            } catch (\Exception $e) {
                // ignore image attach failures during seeding
            }
        }
    }
}
