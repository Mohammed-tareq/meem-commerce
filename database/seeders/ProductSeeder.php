<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Shop;
use Marvel\Enums\ProductStatus;
use Marvel\Enums\ProductType;
use Marvel\Enums\DiscountType;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $faker = Faker::create();
            $fakerEn = Faker::create('en_US');
            $fakerAr = Faker::create('ar_SA');

            // Get valid enum values
            $statuses = ProductStatus::getValues();
            $productTypes = ProductType::getValues();
            $discountTypes = DiscountType::getValues();

            // Get existing shop
            $shop = Shop::first();
            if (!$shop) {
                $this->command->warn('⚠️ No shops found! Create a shop first.');
                return;
            }

            for ($i = 1; $i <= 500; $i++) {
                $productName = $fakerEn->words(3, true);

                Product::create([
                    'name' => [
                        'en' => $productName,
                        'ar' => $fakerAr->words(3, true),
                    ],
                    'slug' => Str::slug($productName . '-' . $i),
                    'description' => [
                        'en' => $fakerEn->sentence(20),
                        'ar' => $fakerAr->sentence(20),
                    ],
                    'price' => $faker->randomFloat(2, 50, 2000),
                    'sku' => 'PRD-' . Str::uuid(),
                    'quantity' => $faker->numberBetween(0, 200),
                    'sold_quantity' => $faker->numberBetween(0, 200),
                    'in_stock' => $faker->boolean(80),
                    'status' => $faker->randomElement($statuses),
                    // 'product_type' => $faker->randomElement($productTypes),
                    'height' => $faker->numberBetween(5, 50) . 'cm',
                    'width' => $faker->numberBetween(5, 50) . 'cm',
                    'length' => $faker->numberBetween(5, 50) . 'cm',
                    'weight' => $faker->numberBetween(100, 5000) . 'g',
                    'has_flash_sale' => $faker->boolean(30),
                    'has_discount' => $faker->boolean(50),
                    'banner_id' => null,
                    'discount_type' => $faker->randomElement($discountTypes),
                    'amount' => $faker->randomFloat(2, 0, 500),
                    'start_date' => $faker->optional(0.5)->date(),
                    'end_date' => $faker->optional(0.5)->date(),
                    'price_after_discount' => $faker->optional(0.5)->randomFloat(2, 20, 1500),
                    'price_after_flash_sale' => $faker->optional(0.5)->randomFloat(2, 20, 1500),
                    'shop_id' => $shop->id,
                ]);
            }

            $this->command->info('✅ ProductSeeder completed successfully! Created 10 products.');
        } catch (\Exception $e) {
            $this->command->error('❌ ProductSeeder failed: ' . $e->getMessage());
            \Log::error('ProductSeeder Error: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            throw $e;
        }
    }
}