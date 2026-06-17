<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\SectionType;
use Marvel\Database\Models\SectionTypeSetting;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'type' => 'banners',
                'front' => ['autoplay' => true, 'slider_speed' => 5000],
                'back'  => ['slug' => null],
            ],
            [
                'type' => 'sliders',
                'front' => ['autoplay' => true, 'slider_speed' => 5000],
                'back'  => [],
            ],
            [
                'type' => 'promotions',
                'front' => ['columns_count' => 4],
                'back'  => [],
            ],
            [
                'type' => 'categories',
                'front' => ['columns_count' => 8, 'shape' => 'circle'],
                'back'  => [],
            ],
            [
                'type' => 'products',
                'front' => ['columns_count' => 5, 'show_timer' => false],
                'back'  => ['limit' => 10, 'type' => 'best_product_sales', 'productsId' => []],
            ],
            [
                'type' => 'flash-sales',
                'front' => ['theme' => 'dark', 'show_timer' => true],
                'back'  => ['status' => 'active', 'flashSalesId' => []],
            ],
            [
                'type' => 'brands',
                'front' => ['columns_count' => 6],
                'back'  => ['limit' => 6, 'brandsId' => []],
            ],
            [
                'type' => 'coupons',
                'front' => ['layout' => 'slider'],
                'back'  => ['only_valid' => true, 'couponsId' => []],
            ],
        ];

        foreach ($types as $item) {
            $sectionType = SectionType::firstOrCreate(['type' => $item['type']]);

            SectionTypeSetting::updateOrCreate(
                ['section_type_id' => $sectionType->id, 'setting_key' => 'front'],
                ['value' => $item['front']]
            );

            SectionTypeSetting::updateOrCreate(
                ['section_type_id' => $sectionType->id, 'setting_key' => 'back'],
                ['value' => $item['back']]
            );
        }
    }
}
