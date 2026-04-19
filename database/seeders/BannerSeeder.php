<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => ['en' => 'Summer Sale', 'ar' => 'تخفيضات الصيف'],
                'description' => ['en' => 'Up to 50% off on all items', 'ar' => 'خصومات تصل إلى 50% على جميع المنتجات'],
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'New Collection', 'ar' => 'المجموعة الجديدة'],
                'description' => ['en' => 'Discover our latest fashion trends', 'ar' => 'اكتشف أحدث صيحات الموضة لدينا'],
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Ramadan Offers', 'ar' => 'عروض رمضان'],
                'description' => ['en' => 'Special discounts during Ramadan', 'ar' => 'خصومات خاصة خلال شهر رمضان'],
                'is_active' => false,
            ],
            [
                'title' => ['en' => 'Winter Clearance', 'ar' => 'تصفية الشتاء'],
                'description' => ['en' => 'Clearance sale on winter clothes', 'ar' => 'تخفيضات على ملابس الشتاء'],
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Black Friday Deals', 'ar' => 'عروض الجمعة السوداء'],
                'description' => ['en' => 'Massive discounts on electronics', 'ar' => 'خصومات ضخمة على الإلكترونيات'],
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Back to School', 'ar' => 'العودة إلى المدرسة'],
                'description' => ['en' => 'Special offers on school supplies', 'ar' => 'عروض خاصة على مستلزمات المدارس'],
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Flash Sale', 'ar' => 'تخفيضات سريعة'],
                'description' => ['en' => 'Limited time flash sale', 'ar' => 'تخفيضات لفترة محدودة'],
                'is_active' => false,
            ],
            [
                'title' => ['en' => 'Valentine’s Day', 'ar' => 'عيد الحب'],
                'description' => ['en' => 'Romantic gifts and offers', 'ar' => 'هدايا وعروض رومانسية'],
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Eid Al-Fitr Sale', 'ar' => 'تخفيضات عيد الفطر'],
                'description' => ['en' => 'Celebrate Eid with special discounts', 'ar' => 'احتفل بالعيد مع خصومات خاصة'],
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Cyber Monday', 'ar' => 'سايبر مانداي'],
                'description' => ['en' => 'Exclusive online deals', 'ar' => 'عروض حصرية عبر الإنترنت'],
                'is_active' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
