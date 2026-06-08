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

            $englishWords = ['premium','classic','modern','smart','wireless','compact','deluxe','elegant','kitchen','home','office','comfort','fresh','eco','durable','essential','luxury','pro'];

            $arabicWords = ['ممتاز','كلاسيك','حديث','ذكي','لاسلكي','مدمج','فاخر','أنيق','مطبخ','منزل','مكتب','راحة','جديد','اقتصادي','متين','أساسي','فخم','احترافي'];

            $productImages = collect(File::files(public_path('images/products')));
            $productImagesCount = $productImages->count();

            $weeklyFlashSales = FlashSale::query()
                ->valid()
                ->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->get();

            $weeklyFlashSalesCount = $weeklyFlashSales->count();

            $categories = Category::pluck('id')->toArray();
            $banners = Banner::pluck('id')->toArray();

            for ($i = 1; $i <= 100; $i++) {

                $productNameEn = $this->randomWords($englishWords, 3);
                $productNameAr = $this->randomWords($arabicWords, 3);

                $hasFlashSale = $this->randomBool(30);

                $product = Product::create([
                    'name' => [
                        'en' => $productNameEn,
                        'ar' => $productNameAr,
                    ],

                    'slug' => [
                        'en' => Str::slug($productNameEn) . '-' . Str::random(5),
                        'ar' => Str::slug($productNameAr) . '-' . Str::random(5),
                    ],

                    'description' => [
                        'en' => $this->randomSentence($englishWords, 20),
                        'ar' => $this->randomSentence($arabicWords, 20),
                    ],

                    'price' => $this->randomFloat(50, 2000),
                    'sku' => 'PRD-' . Str::uuid(),

                    'stock_quantity' => random_int(0, 200),
                    'reserved_quantity' => 0,
                    'pieces' => random_int(1, 10),
                    'sold_quantity' => random_int(0, 200),

                    'in_stock' => $this->randomBool(80),
                    'status' => $this->randomElement([1, 0]),

                    // ✅ FIX: no units
                    'height' => random_int(5, 50),
                    'width' => random_int(5, 50),
                    'length' => random_int(5, 50),
                    'weight' => random_int(100, 5000),

                    'has_flash_sale' => $hasFlashSale,
                    'has_discount' => $this->randomBool(50),

                    'banner_id' => $this->randomElement($banners),

                    'discount_type' => $this->randomElement($discountTypes),
                    'discount_amount' => $this->randomFloat(0, 500),

                    'start_date' => $this->maybeDate(50),
                    'end_date' => $this->maybeDate(50),

                    'price_after_discount' => null,
                    'price_after_flash_sale' => null,
                ]);

                // images
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

                // flash sale
                $flashSale = ($hasFlashSale && $weeklyFlashSalesCount > 0)
                    ? $weeklyFlashSales[$i % $weeklyFlashSalesCount]
                    : null;

                $product->update([
                    'has_flash_sale' => (bool) $flashSale,
                ]);

                if ($flashSale) {
                    $product->flash_sales()->attach($flashSale->id);
                }

                // category safe attach
                if (!empty($categories)) {
                    $product->categories()->attach(
                        $categories[array_rand($categories)]
                    );
                }

                // pricing
                $pricing = $pricingService->calculateProductPricing($product, $flashSale);

                $product->update([
                    'price_after_discount' => $pricing['price_after_discount'] ?? null,
                    'price_after_flash_sale' => $pricing['price_after_flash_sale'] ?? null,
                ]);
            }

            $this->command->info('ProductSeeder completed successfully.');

        } catch (\Exception $e) {
            $this->command->error('ProductSeeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function randomElement(array $items)
    {
        return empty($items) ? null : $items[array_rand($items)];
    }

    private function randomWords(array $pool, int $count): string
    {
        $words = [];
        for ($i = 0; $i < $count; $i++) {
            $words[] = $pool[random_int(0, count($pool) - 1)];
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
        return round($min + (lcg_value() * ($max - $min)), 2);
    }

    private function maybeDate(int $percent): ?string
    {
        if (!$this->randomBool($percent)) {
            return null;
        }

        return now()->addDays(random_int(-30, 90))->toDateString();
    }
}