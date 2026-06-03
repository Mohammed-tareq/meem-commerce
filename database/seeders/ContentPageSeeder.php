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
        $sections = [
            ['type' => 'banners', 'title' => 'Banners', 'endpoint' => '/slider', 'order' => 0],
            ['type' => 'promotions', 'title' => 'Promotions', 'endpoint' => '/promotions', 'order' => 1],
            ['type' => 'pest-category', 'title' => 'Pest Category', 'endpoint' => '/pest-category', 'order' => 2],
            ['type' => 'pest-product-sales', 'title' => 'Pest Product Sales', 'endpoint' => '/pest-product-sales', 'order' => 3],
            ['type' => 'flash-sales', 'title' => 'Flash Sales', 'endpoint' => '/flash-sales', 'order' => 4],
            ['type' => 'flash-sales-qtys', 'title' => 'Flash Sales Qtys', 'endpoint' => '/flash-sales-qtys', 'order' => 5],
            ['type' => 'brand', 'title' => 'Brand', 'endpoint' => '/brand', 'order' => 6],
            ['type' => 'product-brand', 'title' => 'Product Brand', 'endpoint' => '/product-brand', 'order' => 7],
            ['type' => 'product-discount-days', 'title' => 'Product Discount Days', 'endpoint' => '/product-discount-days', 'order' => 8],
            ['type' => 'coupons', 'title' => 'Coupons', 'endpoint' => '/coupons', 'order' => 9],
            ['type' => 'flash-sale-days', 'title' => 'Flash Sale Days', 'endpoint' => '/flash-sale-days', 'order' => 10],
            ['type' => 'parent-category', 'title' => 'Parent Category', 'endpoint' => '/parent-category', 'order' => 11],
            ['type' => 'product-for parent', 'title' => 'Product For Parent', 'endpoint' => '/product-for-parent', 'order' => 12],
            ['type' => 'new-arrivals', 'title' => 'New Arrivals', 'endpoint' => '/new-arrivals', 'order' => 13],
            ['type' => 'all-discount-product', 'title' => 'All Discount Product', 'endpoint' => '/all-discount-product', 'order' => 14],
        ];

        $page->sections()->createMany($sections);
    }
}
