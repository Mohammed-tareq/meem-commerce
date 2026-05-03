<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoryImages = collect(File::files(public_path('images/categories')));
        $categoryImagesCount = $categoryImages->count();

        for ($i = 1; $i <= 100; $i++) {
            $slug = Str::slug("category-$i");
            $parentCategory = Category::inRandomOrder()->first();

            $category = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => [
                        'ar' => "�������� $i",
                        'en' => "Category $i",
                    ],
                    'details' => [
                        'ar' => "������ ������� ��� $i",
                        'en' => "Details of category number $i",
                    ],
                    'parent_id' => $parentCategory?->id,
                ]
            );

            if ($categoryImagesCount > 0 && ! $category->hasMedia('categories')) {
                $image = $categoryImages[($i - 1) % $categoryImagesCount];
                $category
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('categories', 'categories');
            }

            $category->shops()->syncWithoutDetaching([1, 2]);
        }
    }
}
