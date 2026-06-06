<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Models\Section;

class SectionSeeder extends Seeder
{
    public function run()
    {
        $items = [
            [
                'type' => 'banners',
                'title' => ['en' => 'Banners', 'ar' => 'بنرات'],
                'endpoint' => 'banners',
                'order' => 0,
            ],
            [
                'type' => 'promotions',
                'title' => ['en' => 'Promotions', 'ar' => 'عروض'],
                'endpoint' => 'promotions',
                'order' => 1,
            ],
            [
                'type' => 'pest-category',
                'title' => ['en' => 'Pest Category', 'ar' => 'تصنيف مكافحة الآفات'],
                'endpoint' => 'categories',
                'order' => 2,
            ],
            [
                'type' => 'pest-product-sales',
                'title' => ['en' => 'Pest Product Sales', 'ar' => 'مبيعات منتجات مكافحة الآفات'],
                'endpoint' => 'products/best-sales',
                'order' => 3,
            ],
            [
                'type' => 'flash-sales',
                'title' => ['en' => 'Flash Sales', 'ar' => 'عروض فلاش'],
                'endpoint' => 'flash-sales',
                'order' => 4,
            ],
            [
                'type' => 'flash-sales-qtys',
                'title' => ['en' => 'Flash Sales Qtys', 'ar' => 'كميات عروض الفلاش'],
                'endpoint' => 'flash-sales-with-products',
                'order' => 5,
            ],
            [
                'type' => 'brand',
                'title' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
                'endpoint' => 'brands',
                'order' => 6,
            ],
            [
                'type' => 'product-brand',
                'title' => ['en' => 'Product Brand', 'ar' => 'ماركة المنتج'],
                'endpoint' => 'brands-with-products',
                'order' => 7,
            ],
            [
                'type' => 'product-discount-days',
                'title' => ['en' => 'Product Discount Days', 'ar' => 'أيام خصم المنتجات'],
                'endpoint' => 'products/discount-ending-today-or-low-stock',
                'order' => 8,
            ],
            [
                'type' => 'coupons',
                'title' => ['en' => 'Coupons', 'ar' => 'كوبونات'],
                'endpoint' => 'coupons',
                'order' => 9,
            ],
            [
                'type' => 'flash-sale-week',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'flash-sales-ending-this-week',
                'order' => 10,
            ],
            [
                'type' => 'parent-category',
                'title' => ['en' => 'Parent Category', 'ar' => 'التصنيف الرئيسي'],
                'endpoint' => 'categories',
                'order' => 11,
            ],
            [
                'type' => 'product-for parent',
                'title' => ['en' => 'Product For Parent', 'ar' => 'منتجات للتصنيف الرئيسي'],
                'endpoint' => 'products/parent-category',
                'order' => 12,
            ],
            [
                'type' => 'new-arrivals',
                'title' => ['en' => 'New Arrivals', 'ar' => 'وصل حديثاً'],
                'endpoint' => 'products/new-arrivals',
                'order' => 13,
            ],
            [
                'type' => 'all-discount-product',
                'title' => ['en' => 'All Discount Product', 'ar' => 'كل المنتجات المخفضة'],
                'endpoint' => 'products/all-discount-products',
                'order' => 14,
            ],
        ];

        foreach ($items as $item) {
            $section = Section::updateOrCreate([
                'type' => $item['type'],
            ], [
                'endpoint' => $item['endpoint'] ?? null,
                'order' => $item['order'] ?? 0,
                'is_active' => true,
            ]);

            // set translations for title (works with Spatie HasTranslations)
            if (method_exists($section, 'setTranslations')) {
                $section->setTranslations('title', $item['title']);
                $section->save();
            } else {
                $section->title = json_encode($item['title']);
                $section->save();
            }
        }
    }
}
