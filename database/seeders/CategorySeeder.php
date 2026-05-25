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
        $totalCategories = 900;
        $rootCategories = 20;
        $maxDepth = 4;
        $categoriesPerParent = 2;

        $createdCategoriesByLevel = [];
        $createdCount = 0;

        for ($level = 1; $level <= $maxDepth && $createdCount < $totalCategories; $level++) {
            if ($level === 1) {
                $count = min($rootCategories, $totalCategories - $createdCount);

                for ($index = 1; $index <= $count; $index++) {
                    $createdCategoriesByLevel[1][] = $this->seedCategory(
                        $createdCount + 1,
                        null,
                        $categoryImages,
                        $categoryImagesCount
                    );

                    $createdCount++;
                }

                continue;
            }

            $parents = $createdCategoriesByLevel[$level - 1] ?? [];

            foreach ($parents as $parentCategory) {
                for ($index = 1; $index <= $categoriesPerParent && $createdCount < $totalCategories; $index++) {
                    $createdCategoriesByLevel[$level][] = $this->seedCategory(
                        $createdCount + 1,
                        $parentCategory,
                        $categoryImages,
                        $categoryImagesCount
                    );

                    $createdCount++;
                }

                if ($createdCount >= $totalCategories) {
                    break 2;
                }
            }
        }
    }

    private function seedCategory(int $sequence, ?Category $parentCategory, $categoryImages, int $categoryImagesCount): Category
    {
        $category = Category::updateOrCreate(
            ['slug' => Str::slug("category-{$sequence}")],
            [
                'name' => [
                    'ar' => "كاتوجوري {$sequence}",
                    'en' => "Category {$sequence}",
                ],
                'details' => [
                    'ar' => "تفاصيل الكاتوجوري {$sequence}",
                    'en' => "Details of category number {$sequence}",
                ],
                'parent_id' => $parentCategory?->id,
            ]
        );

        if ($categoryImagesCount > 0 && ! $category->hasMedia('categories-desktop')) {
            $image = $categoryImages[($sequence - 1) % $categoryImagesCount];

            $category
                ->addMedia($image->getPathname())
                ->preservingOriginal()
                ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                ->toMediaCollection('categories-desktop', 'categories');
        }
        if ($categoryImagesCount > 0 && ! $category->hasMedia('categories-mobile')) {
            $image = $categoryImages[($sequence - 1) % $categoryImagesCount];

            $category
                ->addMedia($image->getPathname())
                ->preservingOriginal()
                ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                ->toMediaCollection('categories-mobile', 'categories');
        }

        // $category->shops()->syncWithoutDetaching([1, 2]);

        return $category;
    }
}
