<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoryImages = collect(File::files(public_path('images/categories')));
        $categoryImagesCount = $categoryImages->count();

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

            if ($categoryImagesCount > 0) {
                $image = $categoryImages[($i - 1) % $categoryImagesCount];
                $category
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('categories', 'categories');
            }

            $category->shops()->attach([1, 2]);
        }
    }
}
