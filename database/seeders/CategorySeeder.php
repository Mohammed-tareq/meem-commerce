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

        // Use a set of realistic root categories (with Arabic translations)
        $realRootCategories = [
            ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
            ['en' => 'Fashion', 'ar' => 'موضة'],
            ['en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ'],
            ['en' => 'Health & Beauty', 'ar' => 'الصحة والجمال'],
            ['en' => 'Sports & Outdoors', 'ar' => 'الرياضة في الهواء الطلق'],
            ['en' => 'Toys & Games', 'ar' => 'ألعاب'],
            ['en' => 'Automotive', 'ar' => 'السيارات'],
            ['en' => 'Baby Products', 'ar' => 'منتجات الأطفال'],
            ['en' => 'Groceries', 'ar' => 'بقالة'],
            ['en' => 'Books', 'ar' => 'كتب'],
            ['en' => 'Computers', 'ar' => 'حاسوب'],
            ['en' => 'Mobile Phones', 'ar' => 'هواتف محمولة'],
            ['en' => 'Furniture', 'ar' => 'أثاث'],
            ['en' => 'Garden & Outdoors', 'ar' => 'الحديقة في الهواء الطلق'],
            ['en' => 'Office Supplies', 'ar' => 'مستلزمات المكتب'],
            ['en' => 'Jewelry', 'ar' => 'مجوهرات'],
            ['en' => 'Shoes', 'ar' => 'أحذية'],
            ['en' => 'Watches', 'ar' => 'ساعات'],
            ['en' => 'Pet Supplies', 'ar' => 'مستلزمات الحيوانات الأليفة'],
            ['en' => 'Musical Instruments', 'ar' => 'آلات موسيقية'],
        ];

        $totalCategories = 50;
        $maxDepth = 4;
        $categoriesPerParent = 2;

        $createdCategoriesByLevel = [];
        $createdCount = 0;

        for ($level = 1; $level <= $maxDepth && $createdCount < $totalCategories; $level++) {
            if ($level === 1) {
                $count = min(count($realRootCategories), $totalCategories - $createdCount);

                for ($index = 0; $index < $count; $index++) {
                    $name = $realRootCategories[$index];

                    $createdCategoriesByLevel[1][] = $this->seedCategory(
                        $createdCount + 1,
                        null,
                        $categoryImages,
                        $categoryImagesCount,
                        $name['en'],
                        $name['ar']
                    );

                    $createdCount++;
                }

                // if more root slots are needed (to reach total), reuse real names with suffix
                while ($createdCount < min($totalCategories, 20)) {
                    $idx = $createdCount % count($realRootCategories);
                    $name = $realRootCategories[$idx];

                    $createdCategoriesByLevel[1][] = $this->seedCategory(
                        $createdCount + 1,
                        null,
                        $categoryImages,
                        $categoryImagesCount,
                        $name['en'] . ' ' . ($createdCount + 1),
                        $name['ar'] . ' ' . ($createdCount + 1)
                    );

                    $createdCount++;
                }

                continue;
            }

            $parents = $createdCategoriesByLevel[$level - 1] ?? [];

            foreach ($parents as $parentCategory) {
                for ($index = 1; $index <= $categoriesPerParent && $createdCount < $totalCategories; $index++) {
                    // Build more realistic subcategory names based on the parent
                    $parentNameEn = is_array($parentCategory->name) ? ($parentCategory->name['en'] ?? (string)$parentCategory->name) : (string)$parentCategory->name;
                    $parentNameAr = is_array($parentCategory->name) ? ($parentCategory->name['ar'] ?? (string)$parentCategory->name) : (string)$parentCategory->name;

                    // Keep subcategory display name the same as the parent (no "- Subcategory" suffix)
                    $subEn = $parentNameEn;
                    $subAr = $parentNameAr;

                    $createdCategoriesByLevel[$level][] = $this->seedCategory(
                        $createdCount + 1,
                        $parentCategory,
                        $categoryImages,
                        $categoryImagesCount,
                        $subEn,
                        $subAr
                    );

                    $createdCount++;
                }

                if ($createdCount >= $totalCategories) {
                    break 2;
                }
            }
        }
    }

    private function seedCategory(int $sequence, ?Category $parentCategory, $categoryImages, int $categoryImagesCount, string $forceEnName = null, string $forceArName = null): Category
    {
        $nameEn = $forceEnName ?? "Category {$sequence}";
        $nameAr = $forceArName ?? "كاتوجوري {$sequence}";

        // Ensure name uniqueness to avoid DB unique constraint on `name`
        $baseEn = $nameEn;
        $baseAr = $nameAr;
        $suffix = 2;

        while (Category::where('name->en', $nameEn)->exists() || Category::where('name->ar', $nameAr)->exists()) {
            $nameEn = $baseEn . ' ' . $suffix;
            $nameAr = $baseAr . ' ' . $suffix;
            $suffix++;
        }

        $slug = Str::slug($baseEn) . '-' . $sequence;

        $category = Category::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => [
                    'ar' => $nameAr,
                    'en' => $nameEn,
                ],
                'details' => [
                    'ar' => "تفاصيل {$nameAr}",
                    'en' => "Details of {$nameEn}",
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
