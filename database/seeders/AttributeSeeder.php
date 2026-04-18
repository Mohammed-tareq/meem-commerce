<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'name' => ['ar' => 'لون', 'en' => 'Color'],
                'values' => [
                    ['value' => ['ar' => 'أحمر', 'en' => 'Red']],
                    ['value' => ['ar' => 'أزرق', 'en' => 'Blue']],
                    ['value' => ['ar' => 'أخضر', 'en' => 'Green']],
                    ['value' => ['ar' => 'أبيض', 'en' => 'White']],
                    ['value' => ['ar' => 'أسود', 'en' => 'Black']],
                    ['value' => ['ar' => 'أصفر', 'en' => 'Yellow']],
                ],
            ],
            [
                'name' => ['ar' => 'المقاس', 'en' => 'Size'],
                'values' => [
                    ['value' => ['ar' => 'صغير', 'en' => 'Small']],
                    ['value' => ['ar' => 'متوسط', 'en' => 'Medium']],
                    ['value' => ['ar' => 'كبير', 'en' => 'Large']],
                    ['value' => ['ar' => 'كبير جدًا', 'en' => 'Extra Large']],
                ],
            ],
            [
                'name' => ['ar' => 'الخامة', 'en' => 'Material'],
                'values' => [
                    ['value' => ['ar' => 'قطن', 'en' => 'Cotton']],
                    ['value' => ['ar' => 'صوف', 'en' => 'Wool']],
                    ['value' => ['ar' => 'جلد', 'en' => 'Leather']],
                    ['value' => ['ar' => 'بوليستر', 'en' => 'Polyester']],
                    ['value' => ['ar' => 'حرير', 'en' => 'Silk']],
                ],
            ],
            [
                'name' => ['ar' => 'الموسم', 'en' => 'Season'],
                'values' => [
                    ['value' => ['ar' => 'صيفي', 'en' => 'Summer']],
                    ['value' => ['ar' => 'شتوي', 'en' => 'Winter']],
                    ['value' => ['ar' => 'ربيعي', 'en' => 'Spring']],
                    ['value' => ['ar' => 'خريفي', 'en' => 'Autumn']],
                ],
            ],
            [
                'name' => ['ar' => 'الستايل', 'en' => 'Style'],
                'values' => [
                    ['value' => ['ar' => 'كاجوال', 'en' => 'Casual']],
                    ['value' => ['ar' => 'رسمي', 'en' => 'Formal']],
                    ['value' => ['ar' => 'رياضي', 'en' => 'Sport']],
                    ['value' => ['ar' => 'حفلات', 'en' => 'Party']],
                ],
            ],
            [
                'name' => ['ar' => 'البراند', 'en' => 'Brand'],
                'values' => [
                    ['value' => ['ar' => 'نايك', 'en' => 'Nike']],
                    ['value' => ['ar' => 'أديداس', 'en' => 'Adidas']],
                    ['value' => ['ar' => 'بوما', 'en' => 'Puma']],
                    ['value' => ['ar' => 'زارا', 'en' => 'Zara']],
                ],
            ],
        ];

        foreach ($attributes as $attr) {
            $attribute = \Marvel\Database\Models\Attribute::create([
                'name'=> $attr['name'],
            ]);
            foreach ($attr['values'] as $value) {
                \Marvel\Database\Models\AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'value' => $value['value'],
                ]);
            }
        }
    }
}
