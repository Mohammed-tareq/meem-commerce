<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\Banner;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Enums\DiscountType;
use Marvel\Services\Pricing\ProductPricingService;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        try {

            $discountTypes = DiscountType::getValues();
            $pricingService = app(ProductPricingService::class);

            $englishWords = [
                'premium',
                'classic',
                'modern',
                'smart',
                'wireless',
                'compact',
                'deluxe',
                'elegant',
                'kitchen',
                'home',
                'office',
                'comfort',
                'fresh',
                'eco',
                'durable',
                'essential',
                'luxury',
                'pro'
            ];

            $arabicWords = [
                'ممتاز',
                'كلاسيك',
                'حديث',
                'ذكي',
                'لاسلكي',
                'مدمج',
                'فاخر',
                'أنيق',
                'مطبخ',
                'منزل',
                'مكتب',
                'راحة',
                'جديد',
                'اقتصادي',
                'متين',
                'أساسي',
                'فخم',
                'احترافي'
            ];

            $productImages = collect(File::files(public_path('images/products')));
            $productImagesCount = $productImages->count();
            $weeklyFlashSales = FlashSale::query()
                ->valid()
                ->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->orderBy('id')
                ->get();
            $weeklyFlashSalesCount = $weeklyFlashSales->count();

            // $shop = Shop::inRandomOrder()->first();
            // if (! $shop) {
            //     $this->command->warn('No shops found. Create a shop first.');
            //     return;
            // }

            for ($i = 1; $i <= 500; $i++) {
                $productNameEn = $this->randomWords($englishWords, 3);
                $productNameAr = $this->randomWords($arabicWords, 3);

                $hasFlashSale = $this->randomBool(30);
                $product = Product::create([
                    'name' => [
                        'en' => $productNameEn,
                        'ar' => $productNameAr,
                    ],
                    'slug' => Str::slug($productNameEn . '-' . $i),
                    'description' => [
                        'en' => $this->randomSentence($englishWords, 20),
                        'ar' => $this->randomSentence($arabicWords, 20),
                    ],
                    'price' => $this->randomFloat(50, 2000),
                    'sku' => 'PRD-' . Str::uuid(),
                    'quantity' => random_int(0, 200),
                    'pieces' => random_int(1, 10),
                    'sold_quantity' => random_int(0, 200),
                    'in_stock' => $this->randomBool(80),
                    'status' => $this->randomElement([0, 1]),
                    'height' => random_int(5, 50) . 'cm',
                    'width' => random_int(5, 50) . 'cm',
                    'length' => random_int(5, 50) . 'cm',
                    'weight' => random_int(100, 5000) . 'g',
                    'has_flash_sale' => $hasFlashSale,
                    'has_discount' => $this->randomBool(50),
                    'banner_id' => Banner::inRandomOrder()->first()->id,
                    'discount_type' => $this->randomElement($discountTypes),
                    'discount_amount' => $this->randomFloat(0, 500),
                    'start_date' => $this->maybeDate(50),
                    'end_date' => $this->maybeDate(50),
                    'price_after_discount' => null,
                    'price_after_flash_sale' => null,
                ]);

                if ($productImagesCount > 0) {
                    for ($j = 0; $j < 6; $j++) {
                        $image = $productImages[($i - 1 + $j) % $productImagesCount];
                        $product
                            ->addMedia($image->getPathname())
                            ->preservingOriginal()
                            ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                            ->toMediaCollection('products', 'products');
                    }
                }
                $flashSale = $hasFlashSale && $weeklyFlashSalesCount > 0
                    ? $weeklyFlashSales[$i % $weeklyFlashSalesCount]
                    : null;

                if ($flashSale) {
                    $product->update([
                        'has_flash_sale' => true,
                    ]);
                    $product->flash_sales()->attach($flashSale->id);
                } else {
                    $product->update([
                        'has_flash_sale' => false,
                    ]);
                }

                $product->categories()->attach(Category::inRandomOrder()->first()->id);

                $pricing = $pricingService->calculateProductPricing($product, $flashSale);
                $product->update([
                    'price_after_discount' => $pricing['price_after_discount'],
                    'price_after_flash_sale' => $pricing['price_after_flash_sale'],
                ]);
            }

            $this->command->info('ProductSeeder completed successfully. Created 500 products.');
        } catch (\Exception $e) {
            $this->command->error('ProductSeeder failed: ' . $e->getMessage());
            \Log::error('ProductSeeder Error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            throw $e;
        }
    }

    private function randomElement(array $items)
    {
        if (empty($items)) {
            return null;
        }

        return $items[array_rand($items)];
    }

    private function randomWords(array $pool, int $count): string
    {
        $words = [];
        $maxIndex = count($pool) - 1;

        for ($i = 0; $i < $count; $i++) {
            $words[] = $pool[random_int(0, $maxIndex)];
        }

        return implode(' ', $words);
    }

    private function randomSentence(array $pool, int $wordCount): string
    {
        return ucfirst($this->randomWords($pool, $wordCount)) . '.';
    }

    private function randomBool(int $truePercent): bool
    {
        return random_int(1, 100) <= $truePercent;
    }

    private function randomFloat(float $min, float $max): float
    {
        $value = $min + (lcg_value() * ($max - $min));
        return round($value, 2);
    }

    private function maybeDate(int $percent): ?string
    {
        if (! $this->randomBool($percent)) {
            return null;
        }

        $offsetDays = random_int(-30, 90);
        return now()->addDays($offsetDays)->toDateString();
    }
}
