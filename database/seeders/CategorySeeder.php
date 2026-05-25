<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\Category;

class CategorySeeder extends Seeder
{
    private int $sequenceCounter = 1;

    public function run(): void
    {
        $categoryImages = collect(File::files(public_path('images/categories')));
        $categoryImagesCount = $categoryImages->count();

        // Structured categories matching the provided image (English + Arabic)
        $structured = [
            [
                'name' => ['en' => 'Fresh Food', 'ar' => 'أطعمة طازجة'],
                'children' => [
                    ['name' => ['en' => 'Vegetables & Fruits', 'ar' => 'الخضار والفواكه'], 'children' => [
                        ['name' => ['en' => 'Leafy Greens', 'ar' => 'خضراوات ورقية']],
                        ['name' => ['en' => 'Root Vegetables', 'ar' => 'خضروات جذرية']],
                        ['name' => ['en' => 'Fresh Fruits', 'ar' => 'فواكه طازجة']],
                    ]],
                    ['name' => ['en' => 'Dairy & Eggs', 'ar' => 'منتجات الألبان والبيض'], 'children' => [
                        ['name' => ['en' => 'Milk & Alternatives', 'ar' => 'حليب ومشتقاته']],
                        ['name' => ['en' => 'Cheese', 'ar' => 'جبن']],
                        ['name' => ['en' => 'Eggs', 'ar' => 'بيض']],
                    ]],
                    ['name' => ['en' => 'Meat & Poultry', 'ar' => 'اللحوم والدواجن'], 'children' => [
                        ['name' => ['en' => 'Beef', 'ar' => 'لحم بقر']],
                        ['name' => ['en' => 'Chicken', 'ar' => 'دواجن']],
                        ['name' => ['en' => 'Lamb', 'ar' => 'لحم ضأن']],
                    ]],
                    ['name' => ['en' => 'Seafood', 'ar' => 'السمك والمأكولات البحرية'], 'children' => [
                        ['name' => ['en' => 'Fish', 'ar' => 'أسماك']],
                        ['name' => ['en' => 'Shellfish', 'ar' => 'محاريات']],
                    ]],
                    ['name' => ['en' => 'Bakery', 'ar' => 'مخبوزات'], 'children' => [
                        ['name' => ['en' => 'Bread', 'ar' => 'خبز']],
                        ['name' => ['en' => 'Pastries', 'ar' => 'معجنات']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Supermarket', 'ar' => 'السوبر ماركت'],
                'children' => [
                    ['name' => ['en' => 'Snacks & Biscuits', 'ar' => 'بسكويت ووجبات خفيفة'], 'children' => [
                        ['name' => ['en' => 'Chips & Crisps', 'ar' => 'رقائق']],
                        ['name' => ['en' => 'Chocolate & Sweets', 'ar' => 'شوكولاتة وحلويات']],
                    ]],
                    ['name' => ['en' => 'Breakfast & Cereals', 'ar' => 'إفطار وحبوب'], 'children' => [
                        ['name' => ['en' => 'Oats & Porridge', 'ar' => 'شوفان']],
                        ['name' => ['en' => 'Cereals', 'ar' => 'حبوب']],
                    ]],
                    ['name' => ['en' => 'Rice & Pasta', 'ar' => 'أرز ومعكرونة'], 'children' => [
                        ['name' => ['en' => 'White Rice', 'ar' => 'أرز أبيض']],
                        ['name' => ['en' => 'Pasta', 'ar' => 'معكرونة']],
                    ]],
                    ['name' => ['en' => 'Cooking Essentials', 'ar' => 'مكونات الطبخ'], 'children' => [
                        ['name' => ['en' => 'Oils & Vinegar', 'ar' => 'زيوت وخل']],
                        ['name' => ['en' => 'Spices & Seasonings', 'ar' => 'بهارات وتوابل']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Drinks', 'ar' => 'المشروبات'],
                'children' => [
                    ['name' => ['en' => 'Coffee', 'ar' => 'قهوة'], 'children' => [
                        ['name' => ['en' => 'Ground Coffee', 'ar' => 'قهوة مطحونة']],
                        ['name' => ['en' => 'Instant Coffee', 'ar' => 'قهوة سريعة']],
                    ]],
                    ['name' => ['en' => 'Tea', 'ar' => 'شاي'], 'children' => [
                        ['name' => ['en' => 'Black Tea', 'ar' => 'شاي أسود']],
                        ['name' => ['en' => 'Herbal Tea', 'ar' => 'شاي أعشاب']],
                    ]],
                    ['name' => ['en' => 'Juices', 'ar' => 'عصائر'], 'children' => [
                        ['name' => ['en' => 'Fruit Juices', 'ar' => 'عصائر فواكه']],
                        ['name' => ['en' => 'Vegetable Juices', 'ar' => 'عصائر خضار']],
                    ]],
                    ['name' => ['en' => 'Water', 'ar' => 'ماء'], 'children' => [
                        ['name' => ['en' => 'Mineral Water', 'ar' => 'مياه معدنية']],
                        ['name' => ['en' => 'Sparkling Water', 'ar' => 'مياه غازية']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Frozen & Ready Meals', 'ar' => 'أطعمة مجمدة'],
                'children' => [
                    ['name' => ['en' => 'Frozen Vegetables', 'ar' => 'خضروات مجمدة'], 'children' => [
                        ['name' => ['en' => 'Mixed Veg Packs', 'ar' => 'خليط خضروات مجمدة']],
                        ['name' => ['en' => 'Single Veg Packs', 'ar' => 'خضروات مفردة مجمدة']],
                    ]],
                    ['name' => ['en' => 'Ready Meals', 'ar' => 'وجبات جاهزة'], 'children' => [
                        ['name' => ['en' => 'Frozen Pizzas', 'ar' => 'بيتزا مجمدة']],
                        ['name' => ['en' => 'Microwave Meals', 'ar' => 'وجبات ميكروويف']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Health & Beauty', 'ar' => 'الصحة والجمال'],
                'children' => [
                    ['name' => ['en' => 'Skin Care', 'ar' => 'العناية بالبشرة'], 'children' => [
                        ['name' => ['en' => 'Facial Care', 'ar' => 'العناية بالوجه']],
                        ['name' => ['en' => 'Body Care', 'ar' => 'العناية بالجسم']],
                    ]],
                    ['name' => ['en' => 'Hair Care', 'ar' => 'العناية بالشعر'], 'children' => [
                        ['name' => ['en' => 'Shampoos', 'ar' => 'شامبو']],
                        ['name' => ['en' => 'Conditioners', 'ar' => 'بلسم']],
                    ]],
                    ['name' => ['en' => 'Oral Care', 'ar' => 'العناية بالفم'], 'children' => [
                        ['name' => ['en' => 'Toothpaste', 'ar' => 'معجون اسنان']],
                        ['name' => ['en' => 'Mouthwash', 'ar' => 'مضمضة']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'children' => [
                    ['name' => ['en' => 'Mobile Phones', 'ar' => 'الهواتف المحمولة'], 'children' => [
                        ['name' => ['en' => 'Smartphones', 'ar' => 'هواتف ذكية']],
                        ['name' => ['en' => 'Feature Phones', 'ar' => 'هواتف عادية']],
                    ]],
                    ['name' => ['en' => 'Computers', 'ar' => 'الحواسيب'], 'children' => [
                        ['name' => ['en' => 'Laptops', 'ar' => 'حاسبات محمولة']],
                        ['name' => ['en' => 'Desktops', 'ar' => 'حاسبات مكتبية']],
                    ]],
                    ['name' => ['en' => 'Accessories', 'ar' => 'ملحقات'], 'children' => [
                        ['name' => ['en' => 'Chargers & Cables', 'ar' => 'شواحن وكابلات']],
                        ['name' => ['en' => 'Cases & Covers', 'ar' => 'أغطية وحافظات']],
                    ]],
                ],
            ],
        ];

        // Walk the structure and seed categories recursively
        foreach ($structured as $node) {
            $root = $this->seedCategoryWithChildren($node, null, $categoryImages, $categoryImagesCount);
        }
    }

    private function seedCategoryWithChildren(array $node, ?Category $parent, $categoryImages, int $categoryImagesCount): Category
    {
        $seq = $this->sequenceCounter++;
        $nameEn = $node['name']['en'] ?? null;
        $nameAr = $node['name']['ar'] ?? null;

        $category = $this->seedCategory($seq, $parent, $categoryImages, $categoryImagesCount, $nameEn, $nameAr);

        if (! empty($node['children']) && is_array($node['children'])) {
            foreach ($node['children'] as $child) {
                $this->seedCategoryWithChildren($child, $category, $categoryImages, $categoryImagesCount);
            }
        }

        return $category;
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
