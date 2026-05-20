<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Marvel\Database\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brandImages = collect(File::files(public_path('images/categories')));
        $brandImagesCount = $brandImages->count();

        for ($i = 1; $i <= 50; $i++) {
            $name = 'Brand ' . $i;

            $brand = Brand::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => ['en' => $name],
                    'details' => ['en' => 'Auto-generated brand details.'],
                    'status' => random_int(0, 1),
                ]
            );
            if ($brandImagesCount > 0 && ! $brand->hasMedia('brands-desktop')) {
                $image = $brandImages[($i - 1) % $brandImagesCount];
                $brand
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('brands-desktop', 'brands');
            }
            if ($brandImagesCount > 0 && ! $brand->hasMedia('brands-mobile')) {
                $image = $brandImages[($i - 1) % $brandImagesCount];
                $brand
                    ->addMedia($image->getPathname())
                    ->preservingOriginal()
                    ->usingFileName(Str::uuid() . '.' . $image->getExtension())
                    ->toMediaCollection('brands-mobile', 'brands');
            }
        }
    }
}
