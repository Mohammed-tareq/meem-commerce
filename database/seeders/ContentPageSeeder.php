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
use Marvel\Database\Models\SectionType;
use Marvel\Database\Models\SectionTypeSetting;
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
        $firstPromotionSlug = $activePromotions->first()?->slug ?? 'summer-special-20-off';
        $activeCategories = Category::active()->limit(8)->pluck('id')->toArray();
        $activeBrands = Brand::active()->limit(6)->pluck('id')->toArray();
        $validFlashSales = FlashSale::valid()->limit(5)->pluck('id')->toArray();
        $activeCoupons = Coupon::valid()->limit(5)->pluck('id')->toArray();
        $activeSliders = Slider::active()->limit(5)->pluck('id')->toArray();
        $firstProductSlug = Product::active()->value('slug');

        $items = [
            [
                'type' => 'banners',
                'title' => ['en' => 'Banners', 'ar' => 'بنرات'],
                'endpoint' => 'banners',
                'order' => 0,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'start_date' => '2026-06-01',
                        'end_date' => '2026-06-20',
                        'limit' => 10,
                        'brandsId' => $activeBrands,
                        'order' => 'desc',
                    ],
                ]
            ],
            [
                'type' => 'sliders',
                'title' => ['en' => 'Sliders', 'ar' => 'سلايدر'],
                'endpoint' => 'sliders',
                'order' => 0,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'start_date' => '2026-06-01',
                        'end_date' => '2026-06-20',
                        'limit' => 10,
                        'slidersId' => $activeSliders,
                        'order' => 'desc',
                    ],
                ]
            ],
            [
                'type' => 'promotions',
                'title' => ['en' => 'Promotions', 'ar' => 'عروض'],
                'endpoint' => 'promotions',
                'order' => 1,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'slug' => $firstPromotionSlug,
                        'with_product' => true,
                        'order' => 'desc',
                    ],
                ]
            ],
            [
                'type' => 'categories',
                'title' => ['en' => 'Best Category', 'ar' => 'أفضل التصنيفات'],
                'endpoint' => 'categories',
                'order' => 2,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'parent' => true,
                        'limit' => 10,
                        'categoriesId' => $activeCategories,
                        'order' => 'desc',
                    ],
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Best Product Sales', 'ar' => 'أفضل مبيعات المنتجات'],
                'endpoint' => 'products',
                'order' => 3,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'limit' => 20,
                        'order' => 'desc',
                        'order_price' => 'asc',
                        'type' => null,
                        'productsId' => null,
                        'categoriesId' => null,
                        'brandsId' => null,
                        'promotionsId' => null,
                        'flashSalesId' => null,
                        'bannersId' => null,
                        'couponsId' => null,
                    ],
                ]
            ],
            [
                'type' => 'flash-sales',
                'title' => ['en' => 'Flash Sales', 'ar' => 'عروض فلاش'],
                'endpoint' => 'flash-sales',
                'order' => 4,
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'start_date' => '2026-06-01',
                        'end_date' => '2026-06-20',
                        'limit' => 10,
                        'flashSalesId' => $validFlashSales,
                        'order' => 'desc',
                    ],
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Flash Sales Products', 'ar' => 'منتجات عروض الفلاش'],
                'endpoint' => 'products',
                'order' => 5,
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
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'start_date' => '2026-06-01',
                        'end_date' => '2026-06-20',
                        'limit' => 10,
                        'brandsId' => $activeBrands,
                        'order' => 'desc',
                    ],
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Brands Products', 'ar' => 'منتجات العلامات التجارية'],
                'endpoint' => 'products',
                'order' => 7,
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
                'setting' => [
                    'front' => ['autoplay' => true, 'slider_speed' => 5000],
                    'back' => [
                        'start_date' => '2026-06-01',
                        'end_date' => '2026-06-20',
                        'limit' => 10,
                        'couponsId' => $activeCoupons,
                        'order' => 'desc',
                    ],
                ]
            ],
            [
                'type' => 'products',
                'title' => ['en' => 'Flash Sale Days', 'ar' => 'أيام عروض الفلاش'],
                'endpoint' => 'products',
                'order' => 10,
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
                'setting' => [
                    'front' => ['columns_count' => 5],
                    'back' => ['limit' => 10, 'type' => 'all_product_discounts', 'productsId' => []]
                ]
            ],
        ];

        $page->sections()->delete();

        $createdTypes = [];
        foreach ($items as $item) {
            $settingData = $item['setting'] ?? [];
            unset($item['setting']);

            $page->sections()->create($item);

            if (!in_array($item['type'], $createdTypes)) {
                $sectionType = SectionType::firstOrCreate(['type' => $item['type']]);

                SectionTypeSetting::where('section_type_id', $sectionType->id)->delete();

                if ($front = $settingData['front'] ?? null) {
                    SectionTypeSetting::create([
                        'section_type_id' => $sectionType->id,
                        'setting_key' => 'front',
                        'value' => $front,
                    ]);
                }
                if ($back = $settingData['back'] ?? []) {
                    SectionTypeSetting::create([
                        'section_type_id' => $sectionType->id,
                        'setting_key' => 'back',
                        'value' => $back,
                    ]);
                }
                $createdTypes[] = $item['type'];
            }
        }
    }
}
