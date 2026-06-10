<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Models\ContentPage;

class ContentPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = ContentPage::create([
            'title' => 'home',
            'slug' => 'home',
            'is_active' => true,
        ]);
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
                'type' => 'best-category',
                'title' => ['en' => 'Best Category', 'ar' => 'أفضل التصنيفات'],
                'endpoint' => 'categories',
                'order' => 2,
            ],
            [
                'type' => 'best_product_sales',
                'title' => ['en' => 'Best Product Sales', 'ar' => 'أفضل مبيعات المنتجات'],
                'endpoint' => 'products/section?type=best_product_sales',
                'order' => 3,
            ],
            [
                'type' => 'flash-sales',
                'title' => ['en' => 'Flash Sales', 'ar' => 'عروض فلاش'],
                'endpoint' => 'flash-sales',
                'order' => 4,
            ],
            [
                'type' => 'flash_sales_product',
                'title' => ['en' => 'Flash Sales Products', 'ar' => 'منتجات عروض الفلاش'],
                'endpoint' => 'products/section?type=flash_sales_product',
                'order' => 5,
            ],
            [
                'type' => 'brand',
                'title' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
                'endpoint' => 'brands',
                'order' => 6,
            ],
            [
                'type' => 'brands_product',
                'title' => ['en' => 'Brands Products', 'ar' => 'منتجات العلامات التجارية'],
                'endpoint' => 'products/section?type=brands_product',
                'order' => 7,
            ],
            [
                'type' => 'product_discount_today_or_low_qty',
                'title' => ['en' => 'Product Discount Days', 'ar' => 'أيام خصم المنتجات'],
                'endpoint' => 'products/section?type=product_discount_today_or_low_qty',
                'order' => 8,
            ],
            [
                'type' => 'coupons',
                'title' => ['en' => 'Coupons', 'ar' => 'كوبونات'],
                'endpoint' => 'coupons',
                'order' => 9,
            ],
            [
                'type' => 'flash_sales_end_today',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'products/section?type=flash_sales_end_today',
                'order' => 10,
            ],
            [
                'type' => 'flash_sales_end_week',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'products/section?type=flash_sales_end_week',
                'order' => 10,
            ],
            [
                'type' => 'parent_category',
                'title' => ['en' => 'Parent Category', 'ar' => 'التصنيف الرئيسي'],
                'endpoint' => 'categories',
                'order' => 11,
            ],
            [
                'type' => 'product_for_parent_category',
                'title' => ['en' => 'Product For Parent', 'ar' => 'منتجات للتصنيف الرئيسي'],
                'endpoint' => 'products/section?type=product_for_parent_category',
                'order' => 12,
            ],
            [
                'type' => 'new_arrivals',
                'title' => ['en' => 'New Arrivals', 'ar' => 'وصل حديثاً'],
                'endpoint' => 'products/section?type=new_arrivals',
                'order' => 13,
            ],
            [
                'type' => 'all_product_discounts',
                'title' => ['en' => 'All Discount Product', 'ar' => 'كل المنتجات المخفضة'],
                'endpoint' => 'products/section?type=all_product_discounts',
                'order' => 14,
            ],
        ];

        $page->sections()->createMany($items);
    }
}
