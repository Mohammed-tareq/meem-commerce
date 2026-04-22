<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\Shop;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $shopImages = collect(File::files(public_path('images/shops')));
        $shopImagesCount = $shopImages->count();

        for ($i = 1; $i <= 20; $i++) {
            $shop = Shop::create([
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

            if ($shopImagesCount > 0) {
                $logoImage = $shopImages[($i - 1) % $shopImagesCount];
                $coverImage = $shopImages[$i % $shopImagesCount];

                $shop
                    ->addMedia($logoImage->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $logoImage->getExtension())
                    ->toMediaCollection('shop-logo', 'shops');

                $shop
                    ->addMedia($coverImage->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $coverImage->getExtension())
                    ->toMediaCollection('shop-image', 'shops');
            }
        }
    }
}
