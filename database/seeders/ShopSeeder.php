<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Marvel\Database\Models\Shop;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
           Shop::create([
                'owner_id'    => 1,
                'name'        => [
                    'ar' => "المتجر رقم $i",
                    'en' => "Shop number $i",
                ],
                'slug'        => Str::slug("shop-$i"),
                'description' => [
                    'ar' => "وصف المتجر رقم $i بالعربي",
                    'en' => "Description of shop number $i in English",
                ],
                'address'     => [
                    [
                        'city' => [
                            'ar' => 'القاهرة',
                            'en' => 'Cairo',
                        ],
                        'state' => [
                            'ar' => 'الجيزة',
                            'en' => 'Giza',
                        ],
                        'country' => [
                            'ar' => 'مصر',
                            'en' => 'Egypt',
                        ],
                        'street_address' => [
                            'ar' => "شارع التحرير $i",
                            'en' => "Tahrir Street $i",
                        ],
                    ],
                    [
                        'city' => [
                            'ar' => 'الإسكندرية',
                            'en' => 'Alexandria',
                        ],
                        'state' => [
                            'ar' => 'الإسكندرية',
                            'en' => 'Alexandria',
                        ],
                        'country' => [
                            'ar' => 'مصر',
                            'en' => 'Egypt',
                        ],
                        'street_address' => [
                            'ar' => "شارع البحر $i",
                            'en' => "Sea Street $i",
                        ],
                    ],
                ],
               
                'is_active'   => 1,
            ]);
        }
    }
}
