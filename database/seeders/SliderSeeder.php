<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Marvel\Database\Models\Slider;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliderImages = collect(File::files(public_path('images/sliders')));
        $sliderImagesCount = $sliderImages->count();

        $sliders = [
            [
                'title' => ['en' => 'Summer Sale', 'ar' => 'تخفيضات الصيف'],
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'New Collection', 'ar' => 'المجموعة الجديدة'],
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Ramadan Offers', 'ar' => 'عروض رمضان'],
                'order'=> 3,
                'is_active' => false,
            ],
            [
                'title' => ['en' => 'Winter Clearance', 'ar' => 'تصفية الشتاء'],
                'order'=> 4,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Black Friday Deals', 'ar' => 'عروض الجمعة السوداء'],
                'order'=> 5,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Back to School', 'ar' => 'العودة إلى المدرسة'],
                'order'=> 6,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Flash Sale', 'ar' => 'تخفيضات سريعة'],
                'order'=> 7,
                'is_active' => false,
            ],
            [
                'title' => ['en' => 'Valentine’s Day', 'ar' => 'عيد الحب'],
                'order'=> 8,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Eid Al-Fitr Sale', 'ar' => 'تخفيضات عيد الفطر'],
                'order'=> 9,
                'is_active' => true,
            ],
            [
                'title' => ['en' => 'Cyber Monday', 'ar' => 'سايبر مانداي'],
                'order'=> 10,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $index => $slider) {
            $sliderModel = Slider::create($slider);

            if ($sliderImagesCount > 0) {
                $image = $sliderImages[$index % $sliderImagesCount];
                $sliderModel
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('slider-image', 'sliders');
            }
        }
    }
}
