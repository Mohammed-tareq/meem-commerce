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

            $slug = $this->makeUniqueTranslatableSlug(Brand::class, $name, $name);

            $brand = Brand::where('slug->en', $slug['en'])->first();
            if (! $brand) {
                $brand = Brand::create([
                    'name' => ['en' => $name, 'ar' => "{$name} AR"],
                    'details' => ['en' => 'Auto-generated brand details.'],
                    'status' => random_int(0, 1),
                ]);
            } else {
                $brand->update([
                    'name' => ['en' => $name, 'ar' => "{$name} AR"],
                    'details' => ['en' => 'Auto-generated brand details.'],
                    'status' => random_int(0, 1),
                ]);
            }

            // set translatable slug after create/update to avoid sluggable receiving arrays
            $brand->setTranslations('slug', $slug);
            $brand->save();
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

    private function makeUniqueTranslatableSlug(string $modelClass, string $en, string $ar): array
    {
        $baseEn = Str::slug($en ?: 'item');
        $baseAr = str_replace(' ', '-', trim($ar ?: $en));
        $candidate = $baseEn;
        $i = 1;
        while ($modelClass::where('slug->en', $candidate)->exists()) {
            $i++;
            $candidate = $baseEn . '-' . $i;
        }
        $candidateAr = $baseAr;
        if ($i > 1) {
            $candidateAr .= '-' . $i;
        }
        return ['en' => $candidate, 'ar' => $candidateAr];
    }
}
