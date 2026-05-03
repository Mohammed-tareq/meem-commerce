<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Marvel\Database\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 1000; $i++) {
            $discountType = collect(['percentage', 'fixed_rate'])->random();
            $discount = $discountType === 'percentage'
                ? rand(5, 50)
                : rand(10, 200);

            Coupon::create([
                'code'          => strtoupper(Str::random(8)),
                'name'          => [
                    'en' => "Coupon $i",
                    'ar' => "قسيمة $i",
                ],
                'discount'      => $discount,
                'max_discount_amount' => $discountType === 'percentage'
                    ? rand(20, 100)
                    : null,
                'discount_type' => $discountType,
                'start_date'    => Carbon::now()->subDays(rand(0, 5))->format('Y-m-d'),
                'end_date'      => Carbon::now()->addDays(rand(5, 30))->format('Y-m-d'),
                'limiter'       => rand(10, 100),
                'used'          => rand(0, 50),
                'status'        => true,
            ]);
        }
    }
}
