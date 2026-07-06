<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\Banner;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Coupon;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Enums\DiscountType;
use Marvel\Enums\ProductType;
use Marvel\Services\Pricing\ProductPricingService;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $discountTypes = DiscountType::getValues();
            $pricingService = app(ProductPricingService::class);

            $productImages = collect(File::files(public_path('images/products')));
            $productImagesCount = $productImages->count();

            $weeklyFlashSales = FlashSale::query()
                ->valid()
                ->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->get();

            $weeklyFlashSalesCount = $weeklyFlashSales->count();

            $allCategories = Category::all()->keyBy(function ($cat) {
                return $cat->getTranslation('name', 'en');
            });
            $allCategoriesById = Category::all()->keyBy('id');
            $couponIds = Coupon::pluck('id')->toArray();

            $skuCategoryMap = [
                'VEG' => 'Vegetables & Fruits',
                'FRT' => 'Fresh Fruits',
                'DRY' => 'Dairy & Eggs',
                'MET' => 'Meat & Poultry',
                'FRZ' => 'Frozen Vegetables',
                'BAK' => 'Bakery',
                'SNK' => 'Snacks & Biscuits',
                'DRK' => 'Juices',
                'PNT' => 'Rice & Pasta',
                'BRK' => 'Breakfast & Cereals',
                'ELC' => 'Accessories',
                'HOM' => 'Cooking Essentials',
                'BEA' => 'Skin Care',
                'CLO' => 'Men',
                'SPT' => 'Fitness',
                'BBY' => 'Baby Care',
            ];

            $exceptionCategoryMap = [
                'Whole Milk 1L' => 'Milk & Alternatives',
                'Greek Yogurt 500g' => 'Yogurt & Desserts',
                'Cheddar Cheese 250g' => 'Cheese',
                'Mozzarella Cheese 250g' => 'Cheese',
                'Fresh Eggs 12 Pack' => 'Eggs',
                'Chicken Breast 1kg' => 'Chicken',
                'Ground Beef 500g' => 'Beef',
                'Lamb Chops 1kg' => 'Lamb',
                'Atlantic Salmon Fillet 400g' => 'Fish',
                'Sourdough Bread' => 'Bread',
                'Butter Croissant 4 Pack' => 'Pastries',
                'Whole Wheat Bread' => 'Bread',
                'Potato Chips Original 150g' => 'Chips & Crisps',
                'Tortilla Chips 200g' => 'Chips & Crisps',
                'Mixed Nuts 250g' => 'Nuts & Seeds',
                'Milk Chocolate Bar 100g' => 'Chocolate & Sweets',
                'Chocolate Chip Cookies 200g' => 'Chocolate & Sweets',
                'Cola 2L' => 'Water',
                'Orange Juice 1L' => 'Juices',
                'Sparkling Water 1L' => 'Water',
                'Green Tea Bags 25 Pack' => 'Tea',
                'Ground Coffee 250g' => 'Coffee',
                'Instant Noodles 5 Pack' => 'Snacks & Biscuits',
                'Granola Cereal 500g' => 'Granola & Muesli',
                'White Rice 2kg' => 'White Rice',
                'Penne Pasta 500g' => 'Pasta',
                'Spaghetti 500g' => 'Pasta',
                'Extra Virgin Olive Oil 1L' => 'Oils & Vinegar',
                'Tomato Ketchup 500g' => 'Sauces & Condiments',
                'Mayonnaise 400g' => 'Sauces & Condiments',
                'Mixed Spices 100g' => 'Spices & Seasonings',
                'Pure Honey 500g' => 'Cooking Essentials',
                'White Sugar 1kg' => 'Cooking Essentials',
                'All-Purpose Flour 1kg' => 'Cooking Essentials',
                'Canned Tuna 200g' => 'Cooking Essentials',
                'Wireless Mouse' => 'Computers',
                'USB-C Cable 2m' => 'Accessories',
                'Silicone Phone Case' => 'Accessories',
                'Bluetooth Speaker' => 'Audio & Headphones',
                'HDMI Cable 3m' => 'Accessories',
                'Tempered Glass Screen Protector' => 'Accessories',
                'Power Bank 10000mAh' => 'Accessories',
                'Wireless Earbuds' => 'Audio & Headphones',
                'LED Desk Lamp' => 'Accessories',
                'Extension Cord 5m' => 'Accessories',
                'Shampoo 400ml' => 'Hair Care',
                'Conditioner 400ml' => 'Hair Care',
                'Body Lotion 200ml' => 'Skin Care',
                'Face Moisturizer 50ml' => 'Skin Care',
                'Toothpaste 100g' => 'Oral Care',
                'Deodorant Spray 150ml' => 'Skin Care',
                'Hand Cream 75ml' => 'Skin Care',
                'Lip Balm 5g' => 'Skin Care',
                'Sunscreen SPF50 100ml' => 'Skin Care',
                'Cotton T-Shirt' => 'T-Shirts',
                'Denim Jeans' => 'Pants',
                'Wool Scarf' => 'Accessories',
                'Leather Belt' => 'Accessories',
                'Running Shoes' => 'Fitness',
                'Summer Dress' => 'Dresses',
                'Yoga Mat' => 'Fitness',
                'Stainless Water Bottle 1L' => 'Fitness',
                'Jump Rope' => 'Fitness',
                'Resistance Bands Set' => 'Fitness',
                'Dumbbell Set 5kg' => 'Fitness',
                'Baby Diapers Midi 44 Pack' => 'Baby Care',
                'Baby Wipes 80 Pack' => 'Baby Care',
                'Baby Shampoo 200ml' => 'Baby Care',
                'Baby Lotion 200ml' => 'Baby Care',
                'Baby Oil 200ml' => 'Baby Care',
                'Frozen Mixed Vegetables 1kg' => 'Frozen Vegetables',
                'Vanilla Ice Cream 1L' => 'Ice Cream & Desserts',
                'Frozen Pizza Margherita' => 'Frozen Pizzas',
                'Frozen Chicken Nuggets 500g' => 'Frozen Vegetables',
                'Frozen Fish Fingers 400g' => 'Frozen Vegetables',
                'Frozen Waffles 6 Pack' => 'Ice Cream & Desserts',
                'Fresh Spinach Bunch' => 'Leafy Greens',
                'Romaine Lettuce' => 'Leafy Greens',
                'Kale Greens' => 'Leafy Greens',
                'Broccoli Crown' => 'Vegetables & Fruits',
                'Baby Carrots 1kg' => 'Root Vegetables',
                'Roma Tomatoes 1kg' => 'Vegetables & Fruits',
                'English Cucumber' => 'Vegetables & Fruits',
                'Red Onions 1kg' => 'Root Vegetables',
                'Russet Potatoes 2kg' => 'Root Vegetables',
                'Gala Apples 1kg' => 'Fresh Fruits',
                'Bananas 1kg' => 'Fresh Fruits',
                'Navel Oranges 1kg' => 'Fresh Fruits',
                'Red Grapes 500g' => 'Fresh Fruits',
                'Fresh Strawberries 250g' => 'Fresh Fruits',
                'Ripe Mangoes 1kg' => 'Fresh Fruits',
                'Organic Mixed Salad 250g' => 'Organic Produce',
                'Fresh Mint Bunch' => 'Vegetables & Fruits',
            ];

            $variablePrefixes = ['ELC', 'BEA', 'CLO', 'SPT'];

            $productDimensions = [
                // Electronics & Accessories
                'Wireless Mouse'              => ['h' => 12, 'w' => 7,  'l' => 3,  'wt' => 100],
                'USB-C Cable 2m'              => ['h' => 10, 'w' => 5,  'l' => 5,  'wt' => 50],
                'Silicone Phone Case'         => ['h' => 16, 'w' => 8,  'l' => 1,  'wt' => 30],
                'Bluetooth Speaker'           => ['h' => 10, 'w' => 10, 'l' => 18, 'wt' => 500],
                'HDMI Cable 3m'               => ['h' => 12, 'w' => 8,  'l' => 4,  'wt' => 150],
                'Tempered Glass Screen Protector' => ['h' => 18, 'w' => 9,  'l' => 0.1, 'wt' => 20],
                'Power Bank 10000mAh'         => ['h' => 15, 'w' => 8,  'l' => 2,  'wt' => 250],
                'Wireless Earbuds'            => ['h' => 6,  'w' => 5,  'l' => 3,  'wt' => 50],
                'LED Desk Lamp'               => ['h' => 40, 'w' => 20, 'l' => 20, 'wt' => 800],
                'Extension Cord 5m'           => ['h' => 15, 'w' => 10, 'l' => 10, 'wt' => 400],
                // Beauty & Personal Care
                'Shampoo 400ml'               => ['h' => 20, 'w' => 7,  'l' => 7,  'wt' => 450],
                'Conditioner 400ml'           => ['h' => 20, 'w' => 7,  'l' => 7,  'wt' => 450],
                'Body Lotion 200ml'           => ['h' => 16, 'w' => 6,  'l' => 6,  'wt' => 250],
                'Face Moisturizer 50ml'       => ['h' => 12, 'w' => 4,  'l' => 4,  'wt' => 80],
                'Toothpaste 100g'             => ['h' => 16, 'w' => 4,  'l' => 3,  'wt' => 120],
                'Deodorant Spray 150ml'       => ['h' => 15, 'w' => 5,  'l' => 5,  'wt' => 180],
                'Hand Cream 75ml'             => ['h' => 12, 'w' => 4,  'l' => 3,  'wt' => 100],
                'Lip Balm 5g'                => ['h' => 7,  'w' => 2,  'l' => 2,  'wt' => 10],
                'Sunscreen SPF50 100ml'       => ['h' => 14, 'w' => 5,  'l' => 5,  'wt' => 120],
                // Clothing
                'Cotton T-Shirt'              => ['h' => 30, 'w' => 20, 'l' => 2,  'wt' => 200],
                'Denim Jeans'                 => ['h' => 35, 'w' => 25, 'l' => 3,  'wt' => 600],
                'Wool Scarf'                  => ['h' => 25, 'w' => 15, 'l' => 3,  'wt' => 150],
                'Leather Belt'                => ['h' => 10, 'w' => 10, 'l' => 3,  'wt' => 200],
                'Running Shoes'               => ['h' => 30, 'w' => 20, 'l' => 12, 'wt' => 800],
                'Summer Dress'                => ['h' => 30, 'w' => 20, 'l' => 2,  'wt' => 250],
                // Fitness & Sports
                'Yoga Mat'                    => ['h' => 60, 'w' => 15, 'l' => 15, 'wt' => 1000],
                'Stainless Water Bottle 1L'   => ['h' => 25, 'w' => 8,  'l' => 8,  'wt' => 350],
                'Jump Rope'                   => ['h' => 20, 'w' => 10, 'l' => 5,  'wt' => 150],
                'Resistance Bands Set'        => ['h' => 15, 'w' => 10, 'l' => 5,  'wt' => 200],
                'Dumbbell Set 5kg'            => ['h' => 30, 'w' => 15, 'l' => 15, 'wt' => 5000],
            ];

            $categoryDimensionDefaults = [
                'VEG' => ['h' => 15, 'w' => 10, 'l' => 5,  'wt' => 250],
                'FRT' => ['h' => 12, 'w' => 10, 'l' => 8,  'wt' => 300],
                'DRY' => ['h' => 10, 'w' => 7,  'l' => 7,  'wt' => 400],
                'MET' => ['h' => 8,  'w' => 6,  'l' => 4,  'wt' => 600],
                'FRZ' => ['h' => 20, 'w' => 15, 'l' => 10, 'wt' => 800],
                'BAK' => ['h' => 18, 'w' => 12, 'l' => 8,  'wt' => 350],
                'SNK' => ['h' => 12, 'w' => 8,  'l' => 4,  'wt' => 200],
                'DRK' => ['h' => 22, 'w' => 7,  'l' => 7,  'wt' => 1000],
                'PNT' => ['h' => 18, 'w' => 12, 'l' => 6,  'wt' => 500],
                'BRK' => ['h' => 20, 'w' => 14, 'l' => 6,  'wt' => 450],
                'HOM' => ['h' => 22, 'w' => 10, 'l' => 8,  'wt' => 600],
                'BBY' => ['h' => 14, 'w' => 8,  'l' => 6,  'wt' => 250],
            ];

            $products = [
                ['name' => ['en' => 'Fresh Spinach Bunch', 'ar' => 'حزمة سبانخ طازجة'], 'price' => 15.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Romaine Lettuce', 'ar' => 'خس روماني'], 'price' => 12.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Kale Greens', 'ar' => 'كرنب أخضر'], 'price' => 18.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Broccoli Crown', 'ar' => 'بروكلي'], 'price' => 22.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Baby Carrots 1kg', 'ar' => 'جزر صغير 1 كجم'], 'price' => 10.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Roma Tomatoes 1kg', 'ar' => 'طماطم روما 1 كجم'], 'price' => 14.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'English Cucumber', 'ar' => 'خيار إنجليزي'], 'price' => 8.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Red Onions 1kg', 'ar' => 'بصل أحمر 1 كجم'], 'price' => 9.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Russet Potatoes 2kg', 'ar' => 'بطاطس روسيت 2 كجم'], 'price' => 12.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Gala Apples 1kg', 'ar' => 'تفاح جالا 1 كجم'], 'price' => 20.00, 'sku_prefix' => 'FRT'],
                ['name' => ['en' => 'Bananas 1kg', 'ar' => 'موز 1 كجم'], 'price' => 15.00, 'sku_prefix' => 'FRT'],
                ['name' => ['en' => 'Navel Oranges 1kg', 'ar' => 'برتقال سرة 1 كجم'], 'price' => 18.00, 'sku_prefix' => 'FRT'],
                ['name' => ['en' => 'Red Grapes 500g', 'ar' => 'عنب أحمر 500 جم'], 'price' => 25.00, 'sku_prefix' => 'FRT'],
                ['name' => ['en' => 'Fresh Strawberries 250g', 'ar' => 'فراولة طازجة 250 جم'], 'price' => 30.00, 'sku_prefix' => 'FRT'],
                ['name' => ['en' => 'Ripe Mangoes 1kg', 'ar' => 'مانجو ناضج 1 كجم'], 'price' => 35.00, 'sku_prefix' => 'FRT'],
                ['name' => ['en' => 'Whole Milk 1L', 'ar' => 'حليب كامل الدسم 1 لتر'], 'price' => 25.00, 'sku_prefix' => 'DRY'],
                ['name' => ['en' => 'Greek Yogurt 500g', 'ar' => 'زبادي يوناني 500 جم'], 'price' => 30.00, 'sku_prefix' => 'DRY'],
                ['name' => ['en' => 'Cheddar Cheese 250g', 'ar' => 'جبن شيدر 250 جم'], 'price' => 45.00, 'sku_prefix' => 'DRY'],
                ['name' => ['en' => 'Mozzarella Cheese 250g', 'ar' => 'جبن موزاريلا 250 جم'], 'price' => 40.00, 'sku_prefix' => 'DRY'],
                ['name' => ['en' => 'Fresh Eggs 12 Pack', 'ar' => 'بيض طازج 12 حبة'], 'price' => 22.00, 'sku_prefix' => 'DRY'],
                ['name' => ['en' => 'Chicken Breast 1kg', 'ar' => 'صدر دجاج 1 كجم'], 'price' => 120.00, 'sku_prefix' => 'MET'],
                ['name' => ['en' => 'Ground Beef 500g', 'ar' => 'لحم مفروم 500 جم'], 'price' => 85.00, 'sku_prefix' => 'MET'],
                ['name' => ['en' => 'Lamb Chops 1kg', 'ar' => 'أضلاع خروف 1 كجم'], 'price' => 180.00, 'sku_prefix' => 'MET'],
                ['name' => ['en' => 'Atlantic Salmon Fillet 400g', 'ar' => 'فيليه سلمون أتلانتيك 400 جم'], 'price' => 200.00, 'sku_prefix' => 'MET'],
                ['name' => ['en' => 'Frozen Mixed Vegetables 1kg', 'ar' => 'خضروات مشكلة مجمدة 1 كجم'], 'price' => 35.00, 'sku_prefix' => 'FRZ'],
                ['name' => ['en' => 'Vanilla Ice Cream 1L', 'ar' => 'آيس كريم فانيليا 1 لتر'], 'price' => 60.00, 'sku_prefix' => 'FRZ'],
                ['name' => ['en' => 'Frozen Pizza Margherita', 'ar' => 'بيتزا مارجريتا مجمدة'], 'price' => 55.00, 'sku_prefix' => 'FRZ'],
                ['name' => ['en' => 'Sourdough Bread', 'ar' => 'خبز ساور دو'], 'price' => 25.00, 'sku_prefix' => 'BAK'],
                ['name' => ['en' => 'Butter Croissant 4 Pack', 'ar' => 'كرواسون زبدة 4 حبات'], 'price' => 20.00, 'sku_prefix' => 'BAK'],
                ['name' => ['en' => 'Whole Wheat Bread', 'ar' => 'خبز قمح كامل'], 'price' => 18.00, 'sku_prefix' => 'BAK'],
                ['name' => ['en' => 'Potato Chips Original 150g', 'ar' => 'رقائق بطاطس أوريجينال 150 جم'], 'price' => 12.00, 'sku_prefix' => 'SNK'],
                ['name' => ['en' => 'Tortilla Chips 200g', 'ar' => 'رقائق تورتيلا 200 جم'], 'price' => 15.00, 'sku_prefix' => 'SNK'],
                ['name' => ['en' => 'Mixed Nuts 250g', 'ar' => 'مكسرات مشكلة 250 جم'], 'price' => 40.00, 'sku_prefix' => 'SNK'],
                ['name' => ['en' => 'Milk Chocolate Bar 100g', 'ar' => 'شوكولاتة حليب 100 جم'], 'price' => 18.00, 'sku_prefix' => 'SNK'],
                ['name' => ['en' => 'Chocolate Chip Cookies 200g', 'ar' => 'بسكويت شوكولاتة 200 جم'], 'price' => 22.00, 'sku_prefix' => 'SNK'],
                ['name' => ['en' => 'Cola 2L', 'ar' => 'كولا 2 لتر'], 'price' => 12.00, 'sku_prefix' => 'DRK'],
                ['name' => ['en' => 'Orange Juice 1L', 'ar' => 'عصير برتقال 1 لتر'], 'price' => 20.00, 'sku_prefix' => 'DRK'],
                ['name' => ['en' => 'Sparkling Water 1L', 'ar' => 'مياه غازية 1 لتر'], 'price' => 8.00, 'sku_prefix' => 'DRK'],
                ['name' => ['en' => 'Green Tea Bags 25 Pack', 'ar' => 'شاي أخضر 25 كيس'], 'price' => 25.00, 'sku_prefix' => 'DRK'],
                ['name' => ['en' => 'Ground Coffee 250g', 'ar' => 'قهوة مطحونة 250 جم'], 'price' => 85.00, 'sku_prefix' => 'DRK'],
                ['name' => ['en' => 'Instant Noodles 5 Pack', 'ar' => 'نودلز سريعة 5 حبات'], 'price' => 10.00, 'sku_prefix' => 'SNK'],
                ['name' => ['en' => 'White Rice 2kg', 'ar' => 'أرز أبيض 2 كجم'], 'price' => 30.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Penne Pasta 500g', 'ar' => 'بيني باستا 500 جم'], 'price' => 18.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Spaghetti 500g', 'ar' => 'سباجيتي 500 جم'], 'price' => 15.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Extra Virgin Olive Oil 1L', 'ar' => 'زيت زيتون بكر 1 لتر'], 'price' => 120.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Tomato Ketchup 500g', 'ar' => 'كاتشب طماطم 500 جم'], 'price' => 25.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Mayonnaise 400g', 'ar' => 'مايونيز 400 جم'], 'price' => 30.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Mixed Spices 100g', 'ar' => 'بهارات مشكلة 100 جم'], 'price' => 15.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Granola Cereal 500g', 'ar' => 'جرانولا 500 جم'], 'price' => 45.00, 'sku_prefix' => 'BRK'],
                ['name' => ['en' => 'Pure Honey 500g', 'ar' => 'عسل نقي 500 جم'], 'price' => 80.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'White Sugar 1kg', 'ar' => 'سكر أبيض 1 كجم'], 'price' => 18.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'All-Purpose Flour 1kg', 'ar' => 'دقيق متعدد الأغراض 1 كجم'], 'price' => 15.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Canned Tuna 200g', 'ar' => 'تونة معلبة 200 جم'], 'price' => 22.00, 'sku_prefix' => 'PNT'],
                ['name' => ['en' => 'Wireless Mouse', 'ar' => 'ماوس لاسلكي'], 'price' => 250.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'USB-C Cable 2m', 'ar' => 'كابل USB-C 2 م'], 'price' => 75.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'Silicone Phone Case', 'ar' => 'جراب هاتف سيليكون'], 'price' => 100.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'Bluetooth Speaker', 'ar' => 'سماعة بلوتوث'], 'price' => 450.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'HDMI Cable 3m', 'ar' => 'كابل HDMI 3 م'], 'price' => 120.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'Tempered Glass Screen Protector', 'ar' => 'حامي شاشة زجاجي'], 'price' => 60.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'Power Bank 10000mAh', 'ar' => 'باور بانك 10000 مللي أمبير'], 'price' => 350.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'Wireless Earbuds', 'ar' => 'سماعات أذن لاسلكية'], 'price' => 550.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'LED Desk Lamp', 'ar' => 'مصباح مكتب LED'], 'price' => 200.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'Extension Cord 5m', 'ar' => 'سلك تمديد 5 م'], 'price' => 85.00, 'sku_prefix' => 'ELC'],
                ['name' => ['en' => 'Liquid Hand Soap 500ml', 'ar' => 'صابون يد سائل 500 مل'], 'price' => 35.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Dish Soap 750ml', 'ar' => 'سائل جلي 750 مل'], 'price' => 28.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'All-Purpose Cleaner 500ml', 'ar' => 'منظف متعدد الأغراض 500 مل'], 'price' => 32.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Paper Towels 6 Pack', 'ar' => 'مناديل مطبخ 6 حبات'], 'price' => 45.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Trash Bags 50 Pack', 'ar' => 'أكياس قمامة 50 حبة'], 'price' => 30.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Kitchen Sponge 5 Pack', 'ar' => 'ليفة مطبخ 5 حبات'], 'price' => 15.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Laundry Detergent 1L', 'ar' => 'منظف غسيل 1 لتر'], 'price' => 55.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Fabric Softener 1L', 'ar' => 'منعم أقمشة 1 لتر'], 'price' => 45.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Toilet Paper 12 Roll', 'ar' => 'ورق تواليت 12 لفة'], 'price' => 60.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Glass Cleaner 500ml', 'ar' => 'منظف زجاج 500 مل'], 'price' => 25.00, 'sku_prefix' => 'HOM'],
                ['name' => ['en' => 'Shampoo 400ml', 'ar' => 'شامبو 400 مل'], 'price' => 65.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Conditioner 400ml', 'ar' => 'بلسم 400 مل'], 'price' => 65.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Body Lotion 200ml', 'ar' => 'لوشن جسم 200 مل'], 'price' => 55.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Face Moisturizer 50ml', 'ar' => 'مرطب وجه 50 مل'], 'price' => 120.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Toothpaste 100g', 'ar' => 'معجون أسنان 100 جم'], 'price' => 35.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Deodorant Spray 150ml', 'ar' => 'مزيل عرق بخاخ 150 مل'], 'price' => 45.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Hand Cream 75ml', 'ar' => 'كريم يد 75 مل'], 'price' => 30.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Lip Balm 5g', 'ar' => 'مرطب شفاه 5 جم'], 'price' => 15.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Sunscreen SPF50 100ml', 'ar' => 'واقي شمس SPF50 100 مل'], 'price' => 95.00, 'sku_prefix' => 'BEA'],
                ['name' => ['en' => 'Cotton T-Shirt', 'ar' => 'تيشيرت قطني'], 'price' => 150.00, 'sku_prefix' => 'CLO'],
                ['name' => ['en' => 'Denim Jeans', 'ar' => 'جينز دينيم'], 'price' => 350.00, 'sku_prefix' => 'CLO'],
                ['name' => ['en' => 'Wool Scarf', 'ar' => 'وشاح صوفي'], 'price' => 120.00, 'sku_prefix' => 'CLO'],
                ['name' => ['en' => 'Leather Belt', 'ar' => 'حزام جلدي'], 'price' => 200.00, 'sku_prefix' => 'CLO'],
                ['name' => ['en' => 'Running Shoes', 'ar' => 'حذاء جري'], 'price' => 650.00, 'sku_prefix' => 'CLO'],
                ['name' => ['en' => 'Summer Dress', 'ar' => 'فستان صيفي'], 'price' => 280.00, 'sku_prefix' => 'CLO'],
                ['name' => ['en' => 'Yoga Mat', 'ar' => 'سجادة يوجا'], 'price' => 250.00, 'sku_prefix' => 'SPT'],
                ['name' => ['en' => 'Stainless Water Bottle 1L', 'ar' => 'قارورة ماء ستانلس 1 لتر'], 'price' => 80.00, 'sku_prefix' => 'SPT'],
                ['name' => ['en' => 'Jump Rope', 'ar' => 'حبل نط'], 'price' => 45.00, 'sku_prefix' => 'SPT'],
                ['name' => ['en' => 'Resistance Bands Set', 'ar' => 'مجموعة أربطة مقاومة'], 'price' => 120.00, 'sku_prefix' => 'SPT'],
                ['name' => ['en' => 'Dumbbell Set 5kg', 'ar' => 'دمبل 5 كجم'], 'price' => 350.00, 'sku_prefix' => 'SPT'],
                ['name' => ['en' => 'Baby Diapers Midi 44 Pack', 'ar' => 'حفاظات أطفال ميدي 44 حبة'], 'price' => 120.00, 'sku_prefix' => 'BBY'],
                ['name' => ['en' => 'Baby Wipes 80 Pack', 'ar' => 'مناديل أطفال 80 حبة'], 'price' => 35.00, 'sku_prefix' => 'BBY'],
                ['name' => ['en' => 'Baby Shampoo 200ml', 'ar' => 'شامبو أطفال 200 مل'], 'price' => 40.00, 'sku_prefix' => 'BBY'],
                ['name' => ['en' => 'Baby Lotion 200ml', 'ar' => 'لوشن أطفال 200 مل'], 'price' => 45.00, 'sku_prefix' => 'BBY'],
                ['name' => ['en' => 'Baby Oil 200ml', 'ar' => 'زيت أطفال 200 مل'], 'price' => 38.00, 'sku_prefix' => 'BBY'],
                ['name' => ['en' => 'Frozen Chicken Nuggets 500g', 'ar' => 'ناجتس دجاج مجمدة 500 جم'], 'price' => 65.00, 'sku_prefix' => 'FRZ'],
                ['name' => ['en' => 'Frozen Fish Fingers 400g', 'ar' => 'أصابع سمك مجمدة 400 جم'], 'price' => 55.00, 'sku_prefix' => 'FRZ'],
                ['name' => ['en' => 'Frozen Waffles 6 Pack', 'ar' => 'وافل مجمد 6 حبات'], 'price' => 40.00, 'sku_prefix' => 'FRZ'],
                ['name' => ['en' => 'Organic Mixed Salad 250g', 'ar' => 'سلطة مشكلة عضوية 250 جم'], 'price' => 28.00, 'sku_prefix' => 'VEG'],
                ['name' => ['en' => 'Fresh Mint Bunch', 'ar' => 'حزمة نعناع طازج'], 'price' => 5.00, 'sku_prefix' => 'VEG'],
            ];

            foreach ($products as $i => $productData) {
                $productNameEn = $productData['name']['en'];
                $productNameAr = $productData['name']['ar'];
                $basePrice = $productData['price'];
                $skuPrefix = $productData['sku_prefix'];

                $productType = in_array($skuPrefix, $variablePrefixes, true)
                    ? ProductType::VARIABLE
                    : ProductType::SIMPLE;

                $hasFlashSale = $this->randomBool(30);

                $dims = $productDimensions[$productNameEn] ?? $categoryDimensionDefaults[$skuPrefix] ?? [];
                $product = Product::create([
                    'name' => [
                        'en' => $productNameEn,
                        'ar' => $productNameAr,
                    ],
                    'slug' => Str::slug($productNameEn) . '-' . Str::random(5),
                    'description' => [
                        'en' => 'Fresh and high-quality ' . $productNameEn . ' delivered to your doorstep.',
                        'ar' => $productNameAr . ' طازجة وعالية الجودة يتم توصيلها إلى باب منزلك.',
                    ],
                    'price' => $basePrice,
                    'product_type' => $productType,
                    'sku' => $skuPrefix . '-' . Str::uuid(),
                    'stock_quantity' => random_int(10, 200),
                    'reserved_quantity' => 0,
                    'pieces' => 1,
                    'sold_quantity' => random_int(0, 100),
                    'in_stock' => true,
                    'status' => 1,
                    'height' => (string) $dims['h'],
                    'width' => (string) $dims['w'],
                    'length' => (string) $dims['l'],
                    'weight' => (string) $dims['wt'],
                    'has_flash_sale' => $hasFlashSale,
                    'has_discount' => $this->randomBool(30),
                    'discount_type' => $this->randomElement($discountTypes),
                    'discount_amount' => round($basePrice * random_int(5, 30) / 100, 2),
                    'start_date' => $this->maybeDate(30),
                    'end_date' => $this->maybeDate(30),
                    'price_after_discount' => null,
                    'price_after_flash_sale' => null,
                    'is_fast_shipping_available' => random_int(0, 1) == 1 ? true : false,
                ]);

                // images
                if ($productImagesCount > 0) {
                    for ($j = 0; $j < min(4, $productImagesCount); $j++) {
                        $image = $productImages[($i + $j) % $productImagesCount];
                        $product
                            ->addMedia($image->getPathname())
                            ->preservingOriginal()
                            ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                            ->toMediaCollection('products', 'products');
                    }
                }

                // flash sale
                $flashSale = ($hasFlashSale && $weeklyFlashSalesCount > 0)
                    ? $weeklyFlashSales[$i % $weeklyFlashSalesCount]
                    : null;

                $product->update([
                    'has_flash_sale' => (bool) $flashSale,
                ]);

                if ($flashSale) {
                    $product->flash_sales()->attach($flashSale->id);
                }

                // coupon assignment
                if (!empty($couponIds)) {
                    $couponCount = rand(1, min(3, count($couponIds)));
                    $attachedCoupons = (array) array_rand(array_flip($couponIds), $couponCount);
                    $product->coupons()->attach($attachedCoupons);
                }

                // category assignment - match to specific sub-category + all ancestors
                $categoryName = $exceptionCategoryMap[$productNameEn] ?? $skuCategoryMap[$skuPrefix] ?? null;
                if ($categoryName && isset($allCategories[$categoryName])) {
                    $targetCategory = $allCategories[$categoryName];
                    $catIds = [$targetCategory->id];
                    $current = $targetCategory;
                    while ($current->parent_id !== null) {
                        $parent = $allCategoriesById->get($current->parent_id);
                        if (!$parent) break;
                        $catIds[] = $parent->id;
                        $current = $parent;
                    }
                    $product->categories()->attach(array_unique($catIds));
                } elseif (!empty($allCategories)) {
                    $fallback = $allCategories->random();
                    $product->categories()->attach($fallback->id);
                }

                // pricing
                $pricing = $pricingService->calculateProductPricing($product, $flashSale);

                $product->update([
                    'price_after_discount' => $pricing['price_after_discount'] ?? null,
                    'price_after_flash_sale' => $pricing['price_after_flash_sale'] ?? null,
                ]);
            }

            $this->command->info('ProductSeeder completed successfully. Created ' . count($products) . ' products.');

        } catch (\Exception $e) {
            $this->command->error('ProductSeeder failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function randomElement(array $items)
    {
        return empty($items) ? null : $items[array_rand($items)];
    }

    private function randomBool(int $truePercent): bool
    {
        return random_int(1, 100) <= $truePercent;
    }

    private function maybeDate(int $percent): ?string
    {
        if (!$this->randomBool($percent)) {
            return null;
        }

        return now()->addDays(random_int(-30, 90))->toDateString();
    }
}
