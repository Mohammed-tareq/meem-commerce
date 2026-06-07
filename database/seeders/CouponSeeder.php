<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str as SupportStr;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $bannerImages = collect(File::files(public_path('images/banners')));
        $bannerImagesCount = $bannerImages->count();

        for ($i = 1; $i <= 100; $i++) {
            $discountType = collect(['percentage', 'fixed_rate'])->random();
            $discount = $discountType === 'percentage'
                ? rand(5, 50)
                : rand(10, 200);

            $name = [
                'en' => "Coupon $i",
                'ar' => "قسيمة $i",
            ];

            $slug = [
                'en' => "coupon-$i",
                'ar' => "قسيمة-$i",
            ];



            // Insert directly via query builder to avoid model events that set arrays on attributes
            $code = strtoupper(Str::random(8));
            $now = Carbon::now();
            $insert = [
                'code' => $code,
                'name' => json_encode($name),
                'slug' => json_encode($slug),
                'border_color' => sprintf('#%06x', mt_rand(0, 0xFFFFFF)),
                'borderless' => (bool) rand(0, 1),
                'discount' => $discount,
                'max_discount_amount' => $discountType === 'percentage' ? rand(20, 100) : null,
                'discount_type' => $discountType,
                'start_date' => Carbon::now()->subDays(rand(0, 5))->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(rand(5, 30))->format('Y-m-d'),
                'limiter' => rand(10, 100),
                'used' => rand(0, 50),
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('coupons')->insert($insert);

            $coupon = Coupon::where('code', $code)->first();

            if ($bannerImagesCount > 0 && $coupon) {
                $image = $bannerImages[$i % $bannerImagesCount];
                $coupon
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(SupportStr::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('coupons-desktop', 'coupons');
            }
            if ($bannerImagesCount > 0 && $coupon) {
                $image = $bannerImages[$i % $bannerImagesCount];
                $coupon
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(SupportStr::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('coupons-mobile', 'coupons');
            }
        }
    }

    
}
