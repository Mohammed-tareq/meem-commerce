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
                        ['name' => ['en' => 'Organic Produce', 'ar' => 'منتجات عضوية']],
                    ]],
                    ['name' => ['en' => 'Dairy & Eggs', 'ar' => 'منتجات الألبان والبيض'], 'children' => [
                        ['name' => ['en' => 'Milk & Alternatives', 'ar' => 'حليب ومشتقاته']],
                        ['name' => ['en' => 'Cheese', 'ar' => 'جبن']],
                        ['name' => ['en' => 'Eggs', 'ar' => 'بيض']],
                        ['name' => ['en' => 'Yogurt & Desserts', 'ar' => 'زبادي وحلويات']],
                    ]],
                    ['name' => ['en' => 'Meat & Poultry', 'ar' => 'اللحوم والدواجن'], 'children' => [
                        ['name' => ['en' => 'Beef', 'ar' => 'لحم بقر']],
                        ['name' => ['en' => 'Chicken', 'ar' => 'دواجن']],
                        ['name' => ['en' => 'Lamb', 'ar' => 'لحم ضأن']],
                        ['name' => ['en' => 'Processed Meats', 'ar' => 'لحوم مصنعة']],
                    ]],
                    ['name' => ['en' => 'Seafood', 'ar' => 'السمك والمأكولات البحرية'], 'children' => [
                        ['name' => ['en' => 'Fish', 'ar' => 'أسماك']],
                        ['name' => ['en' => 'Shellfish', 'ar' => 'محاريات']],
                        ['name' => ['en' => 'Frozen Seafood', 'ar' => 'مأكولات بحرية مجمدة']],
                    ]],
                    ['name' => ['en' => 'Bakery', 'ar' => 'مخبوزات'], 'children' => [
                        ['name' => ['en' => 'Bread', 'ar' => 'خبز']],
                        ['name' => ['en' => 'Pastries', 'ar' => 'معجنات']],
                        ['name' => ['en' => 'Cakes & Desserts', 'ar' => 'كعك وحلويات']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Supermarket', 'ar' => 'السوبر ماركت'],
                'children' => [
                    ['name' => ['en' => 'Snacks & Biscuits', 'ar' => 'بسكويت ووجبات خفيفة'], 'children' => [
                        ['name' => ['en' => 'Chips & Crisps', 'ar' => 'رقائق']],
                        ['name' => ['en' => 'Chocolate & Sweets', 'ar' => 'شوكولاتة وحلويات']],
                        ['name' => ['en' => 'Nuts & Seeds', 'ar' => 'مكسرات وبذور']],
                    ]],
                    ['name' => ['en' => 'Breakfast & Cereals', 'ar' => 'إفطار وحبوب'], 'children' => [
                        ['name' => ['en' => 'Oats & Porridge', 'ar' => 'شوفان']],
                        ['name' => ['en' => 'Cereals', 'ar' => 'حبوب']],
                        ['name' => ['en' => 'Granola & Muesli', 'ar' => 'غرانولا وميسلي']],
                    ]],
                    ['name' => ['en' => 'Rice & Pasta', 'ar' => 'أرز ومعكرونة'], 'children' => [
                        ['name' => ['en' => 'White Rice', 'ar' => 'أرز أبيض']],
                        ['name' => ['en' => 'Pasta', 'ar' => 'معكرونة']],
                        ['name' => ['en' => 'Brown & Specialty Rice', 'ar' => 'أرز بني ومنتجات خاصة']],
                    ]],
                    ['name' => ['en' => 'Cooking Essentials', 'ar' => 'مكونات الطبخ'], 'children' => [
                        ['name' => ['en' => 'Oils & Vinegar', 'ar' => 'زيوت وخل']],
                        ['name' => ['en' => 'Spices & Seasonings', 'ar' => 'بهارات وتوابل']],
                        ['name' => ['en' => 'Sauces & Condiments', 'ar' => 'صلصات وتوابل']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Drinks', 'ar' => 'المشروبات'],
                'children' => [
                    ['name' => ['en' => 'Coffee', 'ar' => 'قهوة'], 'children' => [
                        ['name' => ['en' => 'Ground Coffee', 'ar' => 'قهوة مطحونة']],
                        ['name' => ['en' => 'Instant Coffee', 'ar' => 'قهوة سريعة']],
                        ['name' => ['en' => 'Cold Brew & Ready Coffee', 'ar' => 'قهوة باردة وجاهزة']],
                    ]],
                    ['name' => ['en' => 'Tea', 'ar' => 'شاي'], 'children' => [
                        ['name' => ['en' => 'Black Tea', 'ar' => 'شاي أسود']],
                        ['name' => ['en' => 'Herbal Tea', 'ar' => 'شاي أعشاب']],
                        ['name' => ['en' => 'Tea Bags & Accessories', 'ar' => 'أكياس الشاي وملحقاتها']],
                    ]],
                    ['name' => ['en' => 'Juices', 'ar' => 'عصائر'], 'children' => [
                        ['name' => ['en' => 'Fruit Juices', 'ar' => 'عصائر فواكه']],
                        ['name' => ['en' => 'Vegetable Juices', 'ar' => 'عصائر خضار']],
                        ['name' => ['en' => 'Smoothies & Blends', 'ar' => 'سموذي وخلطات']],
                    ]],
                    ['name' => ['en' => 'Water', 'ar' => 'ماء'], 'children' => [
                        ['name' => ['en' => 'Mineral Water', 'ar' => 'مياه معدنية']],
                        ['name' => ['en' => 'Sparkling Water', 'ar' => 'مياه غازية']],
                        ['name' => ['en' => 'Flavored Water', 'ar' => 'مياه منكهة']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Frozen & Ready Meals', 'ar' => 'أطعمة مجمدة'],
                'children' => [
                    ['name' => ['en' => 'Frozen Vegetables', 'ar' => 'خضروات مجمدة'], 'children' => [
                        ['name' => ['en' => 'Mixed Veg Packs', 'ar' => 'خليط خضروات مجمدة']],
                        ['name' => ['en' => 'Single Veg Packs', 'ar' => 'خضروات مفردة مجمدة']],
                        ['name' => ['en' => 'Frozen Herbs', 'ar' => 'أعشاب مجمدة']],
                    ]],
                    ['name' => ['en' => 'Ready Meals', 'ar' => 'وجبات جاهزة'], 'children' => [
                        ['name' => ['en' => 'Frozen Pizzas', 'ar' => 'بيتزا مجمدة']],
                        ['name' => ['en' => 'Microwave Meals', 'ar' => 'وجبات ميكروويف']],
                        ['name' => ['en' => 'Ice Cream & Desserts', 'ar' => 'آيس كريم وحلويات']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Health & Beauty', 'ar' => 'الصحة والجمال'],
                'children' => [
                    ['name' => ['en' => 'Skin Care', 'ar' => 'العناية بالبشرة'], 'children' => [
                        ['name' => ['en' => 'Facial Care', 'ar' => 'العناية بالوجه']],
                        ['name' => ['en' => 'Body Care', 'ar' => 'العناية بالجسم']],
                        ['name' => ['en' => 'Makeup', 'ar' => 'مكياج']],
                    ]],
                    ['name' => ['en' => 'Hair Care', 'ar' => 'العناية بالشعر'], 'children' => [
                        ['name' => ['en' => 'Shampoos', 'ar' => 'شامبو']],
                        ['name' => ['en' => 'Conditioners', 'ar' => 'بلسم']],
                        ['name' => ['en' => 'Hair Treatments', 'ar' => 'علاجات الشعر']],
                    ]],
                    ['name' => ['en' => 'Oral Care', 'ar' => 'العناية بالفم'], 'children' => [
                        ['name' => ['en' => 'Toothpaste', 'ar' => 'معجون اسنان']],
                        ['name' => ['en' => 'Mouthwash', 'ar' => 'مضمضة']],
                        ['name' => ['en' => 'Dental Tools', 'ar' => 'أدوات الأسنان']],
                    ]],
                    ['name' => ['en' => 'Fragrances', 'ar' => 'عطور'], 'children' => [
                        ['name' => ['en' => 'Perfumes', 'ar' => 'عطور']],
                        ['name' => ['en' => 'Deodorants', 'ar' => 'مزيلات العرق']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'children' => [
                    ['name' => ['en' => 'Mobile Phones', 'ar' => 'الهواتف المحمولة'], 'children' => [
                        ['name' => ['en' => 'Smartphones', 'ar' => 'هواتف ذكية']],
                        ['name' => ['en' => 'Feature Phones', 'ar' => 'هواتف عادية']],
                        ['name' => ['en' => 'Refurbished Phones', 'ar' => 'هواتف مجددة']],
                    ]],
                    ['name' => ['en' => 'Computers', 'ar' => 'الحواسيب'], 'children' => [
                        ['name' => ['en' => 'Laptops', 'ar' => 'حاسبات محمولة']],
                        ['name' => ['en' => 'Desktops', 'ar' => 'حاسبات مكتبية']],
                        ['name' => ['en' => 'Monitors & Peripherals', 'ar' => 'شاشات وملحقات']],
                    ]],
                    ['name' => ['en' => 'Accessories', 'ar' => 'ملحقات'], 'children' => [
                        ['name' => ['en' => 'Chargers & Cables', 'ar' => 'شواحن وكابلات']],
                        ['name' => ['en' => 'Cases & Covers', 'ar' => 'أغطية وحافظات']],
                        ['name' => ['en' => 'Audio & Headphones', 'ar' => 'صوت وسماعات']],
                    ]],
                    ['name' => ['en' => 'TV & Home Audio', 'ar' => 'تلفاز وصوت منزلي'], 'children' => [
                        ['name' => ['en' => 'Televisions', 'ar' => 'تلفزيونات']],
                        ['name' => ['en' => 'Speakers', 'ar' => 'مكبرات صوت']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Clothing & Accessories', 'ar' => 'ملابس وإكسسوارات'],
                'children' => [
                    ['name' => ['en' => 'Men', 'ar' => 'رجالي'], 'children' => [
                        ['name' => ['en' => 'Shirts', 'ar' => 'قمصان']],
                        ['name' => ['en' => 'T-Shirts', 'ar' => 'تيشيرتات']],
                        ['name' => ['en' => 'Pants', 'ar' => 'بناطيل']],
                    ]],
                    ['name' => ['en' => 'Women', 'ar' => 'نسائي'], 'children' => [
                        ['name' => ['en' => 'Dresses', 'ar' => 'فساتين']],
                        ['name' => ['en' => 'Tops', 'ar' => 'بلوزات']],
                        ['name' => ['en' => 'Skirts', 'ar' => 'تنانير']],
                    ]],
                    ['name' => ['en' => 'Accessories', 'ar' => 'إكسسوارات'], 'children' => [
                        ['name' => ['en' => 'Bags', 'ar' => 'حقائب']],
                        ['name' => ['en' => 'Belts', 'ar' => 'أحزمة']],
                        ['name' => ['en' => 'Hats', 'ar' => 'قبعات']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Baby & Kids', 'ar' => 'أطفال ورضع'],
                'children' => [
                    ['name' => ['en' => 'Baby Care', 'ar' => 'رعاية الطفل'], 'children' => [
                        ['name' => ['en' => 'Diapers', 'ar' => 'حفاظات']],
                        ['name' => ['en' => 'Feeding', 'ar' => 'تغذية']],
                    ]],
                    ['name' => ['en' => 'Kids Clothing', 'ar' => 'ملابس أطفال'], 'children' => [
                        ['name' => ['en' => 'Boys', 'ar' => 'ملابس أولاد']],
                        ['name' => ['en' => 'Girls', 'ar' => 'ملابس بنات']],
                    ]],
                    ['name' => ['en' => 'Toys & Games', 'ar' => 'ألعاب'], 'children' => [
                        ['name' => ['en' => 'Educational Toys', 'ar' => 'ألعاب تعليمية']],
                        ['name' => ['en' => 'Outdoor Play', 'ar' => 'ألعاب خارجية']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Home & Furniture', 'ar' => 'المنزل والأثاث'],
                'children' => [
                    ['name' => ['en' => 'Living Room', 'ar' => 'غرفة المعيشة'], 'children' => [
                        ['name' => ['en' => 'Sofas', 'ar' => 'كنب']],
                        ['name' => ['en' => 'Coffee Tables', 'ar' => 'طاولات قهوة']],
                    ]],
                    ['name' => ['en' => 'Bedroom', 'ar' => 'غرفة النوم'], 'children' => [
                        ['name' => ['en' => 'Beds', 'ar' => 'أسرة']],
                        ['name' => ['en' => 'Wardrobes', 'ar' => 'دولاب']],
                    ]],
                    ['name' => ['en' => 'Kitchen & Dining', 'ar' => 'المطبخ وتناول الطعام'], 'children' => [
                        ['name' => ['en' => 'Dining Sets', 'ar' => 'طقم سفرة']],
                        ['name' => ['en' => 'Cookware', 'ar' => 'أواني طهي']],
                    ]],
                ],
            ],
            [
                'name' => ['en' => 'Sports & Outdoors', 'ar' => 'الرياضة والهواء الطلق'],
                'children' => [
                    ['name' => ['en' => 'Fitness', 'ar' => 'لياقة'], 'children' => [
                        ['name' => ['en' => 'Exercise Equipment', 'ar' => 'معدات رياضية']],
                        ['name' => ['en' => 'Supplements', 'ar' => 'مكملات']],
                    ]],
                    ['name' => ['en' => 'Outdoor', 'ar' => 'نشاطات خارجية'], 'children' => [
                        ['name' => ['en' => 'Camping', 'ar' => 'التخييم']],
                        ['name' => ['en' => 'Cycling', 'ar' => 'ركوب الدراجات']],
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

        // generate unique translatable slug
        $slug = Str::slug($baseEn) . '-' . $sequence;
        $slugEn = $slug;
        $i = 1;
        while (Category::where('slug->en', $slugEn)->exists()) {
            $i++;
            $slugEn = $slug . '-' . $i;
        }
        $slugAr = str_replace(' ', '-', trim($baseAr));
        if ($i > 1) {
            $slugAr .= '-' . $i;
        }

        $existing = Category::where('slug->en', $slugEn)->first();
        if (! $existing) {
            $category = Category::create([
                'slug' => [
                    'ar' => $nameAr,
                    'en' => $nameEn,
                ],
                'name' => [
                    'ar' => $nameAr,
                    'en' => $nameEn,
                ],
                'details' => [
                    'ar' => "تفاصيل {$nameAr}",
                    'en' => "Details of {$nameEn}",
                ],
                'parent_id' => $parentCategory?->id,
            ]);
        } else {
            $existing->update([
                'slug' => [
                    'ar' => $slugAr,
                    'en' => $slugEn,
                ],
                'name' => [
                    'ar' => $nameAr,
                    'en' => $nameEn,
                ],
                'details' => [
                    'ar' => "تفاصيل {$nameAr}",
                    'en' => "Details of {$nameEn}",
                ],
                'parent_id' => $parentCategory?->id,
            ]);
            $category = $existing;
        }

        // set full translatable slug JSON after create/update (avoid relying on HasTranslations)
        $category->setAttribute('slug', json_encode(['en' => $slugEn, 'ar' => $slugAr]));
        $category->save();

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