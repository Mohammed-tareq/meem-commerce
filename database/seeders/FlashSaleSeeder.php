<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\FlashSale;

class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        $flashSaleImages = collect(File::files(public_path('images/banners')));
        $flashSaleImagesCount = $flashSaleImages->count();
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $flashSales = [
            [
                'title' => ['en' => 'This Week Mega Discount', 'ar' => 'خصم ضخم هذا الأسبوع'],
                'description' => ['en' => 'Save big on electronics', 'ar' => 'وفر الكثير على الإلكترونيات'],
                'start_date' => $weekStart,
                'end_date' => $weekEnd,
                'type' => 'percentage',
                'discount' => 20.0,
                'max_discount_amount' => 100.0,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Fashion Week Sale', 'ar' => 'تخفيضات أسبوع الموضة'],
                'description' => ['en' => 'Special offers on clothing', 'ar' => 'عروض خاصة على الملابس'],
                'start_date' => now(),
                'end_date' => now()->addDays(10),
                'type' => 'fixed_rate',
                'discount' => 100.0,
                'max_discount_amount' => null,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Ramadan Offers', 'ar' => 'عروض رمضان'],
                'description' => ['en' => 'Celebrate Ramadan with discounts', 'ar' => 'احتفل برمضان مع خصومات'],
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'type' => 'percentage',
                'discount' => 15.0,
                'max_discount_amount' => 75.0,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Back to School', 'ar' => 'العودة إلى المدرسة'],
                'description' => ['en' => 'Discounts on school supplies', 'ar' => 'خصومات على مستلزمات المدارس'],
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(12),
                'type' => 'fixed_rate',
                'discount' => 50.0,
                'max_discount_amount' => null,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Black Friday', 'ar' => 'الجمعة السوداء'],
                'description' => ['en' => 'Massive discounts on electronics', 'ar' => 'خصومات ضخمة على الإلكترونيات'],
                'start_date' => now(),
                'end_date' => now()->addDays(32),
                'type' => 'percentage',
                'discount' => 40.0,
                'max_discount_amount' => 150.0,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Cyber Monday', 'ar' => 'سايبر مانداي'],
                'description' => ['en' => 'Exclusive online deals', 'ar' => 'عروض حصرية عبر الإنترنت'],
                'start_date' => now()->addDays(33),
                'end_date' => now()->addDays(35),
                'type' => 'percentage',
                'discount' => 25.0,
                'max_discount_amount' => 90.0,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Winter Clearance', 'ar' => 'تصفية الشتاء'],
                'description' => ['en' => 'Clearance sale on winter clothes', 'ar' => 'تخفيضات على ملابس الشتاء'],
                'start_date' => now()->addDays(40),
                'end_date' => now()->addDays(50),
                'type' => 'fixed_rate',
                'discount' => 75.0,
                'max_discount_amount' => null,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Valentine’s Day', 'ar' => 'عيد الحب'],
                'description' => ['en' => 'Romantic gifts and offers', 'ar' => 'هدايا وعروض رومانسية'],
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(62),
                'type' => 'percentage',
                'discount' => 10.0,
                'max_discount_amount' => 50.0,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Eid Al-Fitr Sale', 'ar' => 'تخفيضات عيد الفطر'],
                'description' => ['en' => 'Celebrate Eid with special discounts', 'ar' => 'احتفل بالعيد مع خصومات خاصة'],
                'start_date' => now()->addDays(70),
                'end_date' => now()->addDays(75),
                'type' => 'percentage',
                'discount' => 30.0,
                'max_discount_amount' => 120.0,
                'status' => true,
            ],
            [
                'title' => ['en' => 'Summer Sale', 'ar' => 'تخفيضات الصيف'],
                'description' => ['en' => 'Hot deals for summer', 'ar' => 'عروض ساخنة للصيف'],
                'start_date' => now()->addDays(90),
                'end_date' => now()->addDays(100),
                'type' => 'fixed_rate',
                'discount' => 120.0,
                'max_discount_amount' => null,
                'status' => true,
            ],
        ];

        foreach ($flashSales as $index => $sale) {
            $flashSale = FlashSale::create($sale);

            if ($flashSaleImagesCount > 0 && ! $flashSale->hasMedia('flash-sales-image')) {
                $image = $flashSaleImages[$index % $flashSaleImagesCount];

                $flashSale
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('flash-sales-image', 'flashSales');
            }
        }
    }
}
