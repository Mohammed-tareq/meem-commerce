<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Banner;
use Marvel\Database\Models\Brand;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\Slider;
use Marvel\Models\ContentPage;

class ContentPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = ContentPage::firstOrCreate([
            'slug' => 'home',
        ], [
            'title' => 'home',
            'is_active' => true,
        ]);

        $firstBannerSlug = Banner::active()->value('slug');
        $activePromotions = Promotion::active()->valid()->get();
        $firstPromotionSlug = $activePromotions->first()?->slug;
        $activeCategories = Category::active()->limit(8)->pluck('id')->toArray();
        $activeBrands = Brand::active()->limit(6)->pluck('id')->toArray();
        $validFlashSales = FlashSale::valid()->limit(5)->pluck('id')->toArray();
        $activeCoupons = Coupon::valid()->limit(5)->pluck('id')->toArray();
        $firstProductSlug = Product::active()->value('slug');

        $items = [
            [
                'type' => 'banners',
                'title' => ['en' => 'Banners', 'ar' => 'بنرات'],
                'endpoint' => 'banners',
                'order' => 0,
                'with_product' => true,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => ['slug' => $firstBannerSlug]
                ]
            ],
            [
                'type' => 'sliders',
                'title' => ['en' => 'Sliders', 'ar' => 'سلايدر'],
                'endpoint' => 'sliders',
                'order' => 0,
                'with_product' => false,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => []
                ]
            ],
            [
                'type' => 'promotions',
                'title' => ['en' => 'Promotions', 'ar' => 'عروض'],
                'endpoint' => 'promotions',
                'order' => 1,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 4],
                    'back' => []
                ]
            ],
            [
                'type' => 'categories',
                'title' => ['en' => 'Best Category', 'ar' => 'أفضل التصنيفات'],
                'endpoint' => 'categories',
                'order' => 2,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 8, 'shape' => 'circle'],
                    'back' => []
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Best Product Sales', 'ar' => 'أفضل مبيعات المنتجات'],
                'endpoint' => 'products',
                'order' => 3,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => false],
                    'back' => ['limit' => 10, 'type' => 'best_product_sales', 'productsId' => []]
                ]
            ],
            [
                'type' => 'flash-sales',
                'title' => ['en' => 'Flash Sales', 'ar' => 'عروض فلاش'],
                'endpoint' => 'flash-sales',
                'order' => 4,
                'with_product' => false,
                'setting' => [
                    'front' => ['theme' => 'dark', 'show_timer' => true],
                    'back' => ['status' => 'active', 'flashSalesId' => $validFlashSales],
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Flash Sales Products', 'ar' => 'منتجات عروض الفلاش'],
                'endpoint' => 'products',
                'order' => 5,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => true, 'timer_end_at' => '2026-07-01 00:00:00'],
                    'back' => ['limit' => 10, 'type' => 'flash_sales_product', 'productsId' => []]
                ]
            ],
            [
                'type' => 'brands',
                'title' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
                'endpoint' => 'brands',
                'order' => 6,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 6],
                    'back' => ['limit' => 6, 'brandsId' => $activeBrands]
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Brands Products', 'ar' => 'منتجات العلامات التجارية'],
                'endpoint' => 'products',
                'order' => 7,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5],
                    'back' => ['limit' => 10, 'type' => 'brands_product', 'productsId' => []]
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Product Discount Days', 'ar' => 'أيام خصم المنتجات'],
                'endpoint' => 'products',
                'order' => 8,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5, 'badge_text' => 'LOW STOCK'],
                    'back' => ['limit' => 10, 'type' => 'product_discount_today_or_low_qty', 'productsId' => []]
                ]
            ],
            [
                'type' => 'coupons',
                'title' => ['en' => 'Coupons', 'ar' => 'كوبونات'],
                'endpoint' => 'coupons',
                'order' => 9,
                'with_product' => false,
                'setting' => [
                    'front' => ['layout' => 'slider'],
                    'back' => ['only_valid' => true, 'couponsId' => $activeCoupons]
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'products',
                'order' => 10,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => true],
                    'back' => ['limit' => 10, 'type' => 'flash_sales_end_today', 'productsId' => []]
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'products',
                'order' => 10,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5, 'show_timer' => true],
                    'back' => ['limit' => 10, 'type' => 'flash_sales_end_week', 'productsId' => []]
                ]
            ],
            [
                'type' => 'categories',
                'title' => ['en' => 'Parent Category', 'ar' => 'التصنيف الرئيسي'],
                'endpoint' => 'categories',
                'order' => 11,
                'with_product' => false,
                'setting' => [
                    'front' => ['layout' => 'list'],
                    'back' => ['parent_only' => true, 'categoriesId' => $activeCategories]
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Product For Parent', 'ar' => 'منتجات للتصنيف الرئيسي'],
                'endpoint' => 'products',
                'order' => 12,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5],
                    'back' => ['limit' => 10, 'type' => 'product_for_parent_category', 'productsId' => []]
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'New Arrivals', 'ar' => 'وصل حديثاً'],
                'endpoint' => 'products',
                'order' => 13,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5, 'badge_text' => 'NEW'],
                    'back' => ['limit' => 10, 'type' => 'new_arrivals', 'productsId' => []]
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'All Discount Product', 'ar' => 'كل المنتجات المخفضة'],
                'endpoint' => 'products',
                'order' => 14,
                'with_product' => false,
                'setting' => [
                    'front' => ['columns_count' => 5],
                    'back' => ['limit' => 10, 'type' => 'all_product_discounts', 'productsId' => []]
                ]
            ],
        ];

        $page->sections()->delete();
        $page->sections()->createMany($items);
    }
}
