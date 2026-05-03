<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Marvel\Database\Models\Promotion;
use Marvel\Enums\PromotionType;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            PromotionType::PERCENTAGE,
            PromotionType::FIXED,
            PromotionType::AMOUNT,
        ];

        for ($i = 1; $i <= 50; $i++) {
            $type = $types[array_rand($types)];
            $value = $type === PromotionType::PERCENTAGE
                ? rand(5, 50)
                : rand(5, 200);

            Promotion::create([
                'name' => [
                    'ar' => "عرض ترويجي $i",
                    'en' => "Promotion $i",
                ],
                'type' => $type,
                'value' => $value,
                'max_discount_amount' => $type === PromotionType::PERCENTAGE
                    ? rand(20, 100)
                    : null,
                'code' => (string) Str::uuid(),
                'required_quantity' => $type === PromotionType::AMOUNT ? rand(2, 5) : null,
                'product_id' => null,
                'limiter' => rand(10, 100),
                'usage' => rand(0, 20),
                'start_at' => Carbon::now()->subDays(rand(0, 5)),
                'end_at' => Carbon::now()->addDays(rand(5, 30)),
                'status' => true,
            ]);
        }
    }
}
