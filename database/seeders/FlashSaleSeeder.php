<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Marvel\Database\Models\FlashSale;

class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        $flashSales = [
            [
                'title' => ['en' => 'Mega Discount', 'ar' => 'خصم ضخم'],
                'description' => ['en' => 'Save big on electronics', 'ar' => 'وفر الكثير على الإلكترونيات'],
                'start_date' => now(),
                'end_date' => now()->addDays(7),
                'slug' => Str::slug('Mega Discount'),
                'type' => 'percentage',
                'value' => 20.0,
            ],
            [
                'title' => ['en' => 'Fashion Week Sale', 'ar' => 'تخفيضات أسبوع الموضة'],
                'description' => ['en' => 'Special offers on clothing', 'ar' => 'عروض خاصة على الملابس'],
                'start_date' => now(),
                'end_date' => now()->addDays(10),
                'slug' => Str::slug('Fashion Week Sale'),
                'type' => 'fixed',
                'value' => 100.0,
            ],
            [
                'title' => ['en' => 'Ramadan Offers', 'ar' => 'عروض رمضان'],
                'description' => ['en' => 'Celebrate Ramadan with discounts', 'ar' => 'احتفل برمضان مع خصومات'],
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'slug' => Str::slug('Ramadan Offers'),
                'type' => 'percentage',
                'value' => 15.0,
            ],
            [
                'title' => ['en' => 'Back to School', 'ar' => 'العودة إلى المدرسة'],
                'description' => ['en' => 'Discounts on school supplies', 'ar' => 'خصومات على مستلزمات المدارس'],
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(12),
                'slug' => Str::slug('Back to School'),
                'type' => 'fixed',
                'value' => 50.0,
            ],
            [
                'title' => ['en' => 'Black Friday', 'ar' => 'الجمعة السوداء'],
                'description' => ['en' => 'Massive discounts on electronics', 'ar' => 'خصومات ضخمة على الإلكترونيات'],
                'start_date' => now(),
                'end_date' => now()->addDays(32),
                'slug' => Str::slug('Black Friday'),
                'type' => 'percentage',
                'value' => 40.0,
            ],
            [
                'title' => ['en' => 'Cyber Monday', 'ar' => 'سايبر مانداي'],
                'description' => ['en' => 'Exclusive online deals', 'ar' => 'عروض حصرية عبر الإنترنت'],
                'start_date' => now()->addDays(33),
                'end_date' => now()->addDays(35),
                'slug' => Str::slug('Cyber Monday'),
                'type' => 'percentage',
                'value' => 25.0,
            ],
            [
                'title' => ['en' => 'Winter Clearance', 'ar' => 'تصفية الشتاء'],
                'description' => ['en' => 'Clearance sale on winter clothes', 'ar' => 'تخفيضات على ملابس الشتاء'],
                'start_date' => now()->addDays(40),
                'end_date' => now()->addDays(50),
                'slug' => Str::slug('Winter Clearance'),
                'type' => 'fixed',
                'value' => 75.0,
            ],
            [
                'title' => ['en' => 'Valentine’s Day', 'ar' => 'عيد الحب'],
                'description' => ['en' => 'Romantic gifts and offers', 'ar' => 'هدايا وعروض رومانسية'],
                'start_date' => now()->addDays(60),
                'end_date' => now()->addDays(62),
                'slug' => Str::slug('Valentine’s Day'),
                'type' => 'percentage',
                'value' => 10.0,
            ],
            [
                'title' => ['en' => 'Eid Al-Fitr Sale', 'ar' => 'تخفيضات عيد الفطر'],
                'description' => ['en' => 'Celebrate Eid with special discounts', 'ar' => 'احتفل بالعيد مع خصومات خاصة'],
                'start_date' => now()->addDays(70),
                'end_date' => now()->addDays(75),
                'slug' => Str::slug('Eid Al-Fitr Sale'),
                'type' => 'percentage',
                'value' => 30.0,
            ],
            [
                'title' => ['en' => 'Summer Sale', 'ar' => 'تخفيضات الصيف'],
                'description' => ['en' => 'Hot deals for summer', 'ar' => 'عروض ساخنة للصيف'],
                'start_date' => now()->addDays(90),
                'end_date' => now()->addDays(100),
                'slug' => Str::slug('Summer Sale'),
                'type' => 'fixed',
                'value' => 120.0,
            ],
        ];

        foreach ($flashSales as $sale) {
            FlashSale::create($sale);
        }
    }
}
