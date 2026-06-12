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
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => ['limit' => 5]
                ]
            ],
            [
                'type' => 'promotions',
                'title' => ['en' => 'Promotions', 'ar' => 'عروض'],
                'endpoint' => 'promotions',
                'order' => 1,
                'setting' => [
                    'front' => ['columns_count' => 4],
                    'back' => ['limit' => 4]
                ]
            ],
            [
                'type' => 'best-category',
                'title' => ['en' => 'Best Category', 'ar' => 'أفضل التصنيفات'],
                'endpoint' => 'categories',
                'order' => 2,
                'setting' => [
                    'front' => ['columns_count' => 8, 'shape' => 'circle'],
                    'back' => ['limit' => 8, 'type' => 'featured']
                ]
            ],
            [
                'type' => 'best_product_sales',
                'title' => ['en' => 'Best Product Sales', 'ar' => 'أفضل مبيعات المنتجات'],
                'endpoint' => 'products/section?type=best_product_sales',
                'order' => 3,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => false],
                    'back' => ['limit' => 10, 'sort_by' => 'highest_sales']
                ]
            ],
            [
                'type' => 'flash-sales',
                'title' => ['en' => 'Flash Sales', 'ar' => 'عروض فلاش'],
                'endpoint' => 'flash-sales',
                'order' => 4,
                'setting' => [
                    'front' => ['theme' => 'dark', 'show_timer' => true],
                    'back' => ['status' => 'active']
                ]
            ],
            [
                'type' => 'flash_sales_product',
                'title' => ['en' => 'Flash Sales Products', 'ar' => 'منتجات عروض الفلاش'],
                'endpoint' => 'products/section?type=flash_sales_product',
                'order' => 5,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => true, 'timer_end_at' => '2026-07-01 00:00:00'],
                    'back' => ['limit' => 10, 'has_stock_only' => true]
                ]
            ],
            [
                'type' => 'brand',
                'title' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
                'endpoint' => 'brands',
                'order' => 6,
                'setting' => [
                    'front' => ['columns_count' => 6],
                    'back' => ['limit' => 6]
                ]
            ],
            [
                'type' => 'brands_product',
                'title' => ['en' => 'Brands Products', 'ar' => 'منتجات العلامات التجارية'],
                'endpoint' => 'products/section?type=brands_product',
                'order' => 7,
                'setting' => [
                    'front' => ['columns_count' => 5],
                    'back' => ['limit' => 10]
                ]
            ],
            [
                'type' => 'product_discount_today_or_low_qty',
                'title' => ['en' => 'Product Discount Days', 'ar' => 'أيام خصم المنتجات'],
                'endpoint' => 'products/section?type=product_discount_today_or_low_qty',
                'order' => 8,
                'setting' => [
                    'front' => ['columns_count' => 5, 'badge_text' => 'LOW STOCK'],
                    'back' => ['limit' => 10, 'min_discount' => 15]
                ]
            ],
            [
                'type' => 'coupons',
                'title' => ['en' => 'Coupons', 'ar' => 'كوبونات'],
                'endpoint' => 'coupons',
                'order' => 9,
                'setting' => [
                    'front' => ['layout' => 'grid'],
                    'back' => ['only_valid' => true]
                ]
            ],
            [
                'type' => 'flash_sales_end_today',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'products/section?type=flash_sales_end_today',
                'order' => 10,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => true],
                    'back' => ['limit' => 10, 'timeframe' => 'today']
                ]
            ],
            [
                'type' => 'flash_sales_end_week',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'products/section?type=flash_sales_end_week',
                'order' => 10,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => true],
                    'back' => ['limit' => 10, 'timeframe' => 'week']
                ]
            ],
            [
                'type' => 'parent_category',
                'title' => ['en' => 'Parent Category', 'ar' => 'التصنيف الرئيسي'],
                'endpoint' => 'categories',
                'order' => 11,
                'setting' => [
                    'front' => ['layout' => 'list'],
                    'back' => ['parent_only' => true]
                ]
            ],
            [
                'type' => 'product_for_parent_category',
                'title' => ['en' => 'Product For Parent', 'ar' => 'منتجات للتصنيف الرئيسي'],
                'endpoint' => 'products/section?type=product_for_parent_category',
                'order' => 12,
                'setting' => [
                    'front' => ['columns_count' => 5],
                    'back' => ['limit' => 10, 'category_level' => 'parent']
                ]
            ],
            [
                'type' => 'new_arrivals',
                'title' => ['en' => 'New Arrivals', 'ar' => 'وصل حديثاً'],
                'endpoint' => 'products/section?type=new_arrivals',
                'order' => 13,
                'setting' => [
                    'front' => ['columns_count' => 5, 'badge_text' => 'NEW'],
                    'back' => ['limit' => 10, 'sort_by' => 'latest_created']
                ]
            ],
            [
                'type' => 'all_product_discounts',
                'title' => ['en' => 'All Discount Product', 'ar' => 'كل المنتجات المخفضة'],
                'endpoint' => 'all_product_discounts',
                'order' => 14,
                'setting' => [
                    'front' => ['columns_count' => 5],
                    'back' => ['limit' => 10, 'has_discount' => true]
                ]
            ],
        ];

        $page->sections()->createMany($items);
    }
}