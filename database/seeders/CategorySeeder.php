<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 50; $i++) {
            $category = \Marvel\Database\Models\Category::create([
                'name' => [
                    'ar' => "كاتيجوري $i",
                    'en' => "Category $i",
                ],
                'slug' => Str::slug("category-$i"),
                'details' => [
                    'ar' => "تفاصيل التصنيف رقم $i",
                    'en' => "Details of category number $i",
                ],
            ]);

            $category->shops()->attach([1, 2]);
        }
    }
}
