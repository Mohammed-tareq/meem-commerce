<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Brand;
use Marvel\Database\Models\Product;

class BrandProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::all();
        $productIds = Product::pluck('id');

        if ($brands->isEmpty() || $productIds->isEmpty()) {
            return;
        }

        foreach ($brands as $brand) {
            $maxAttach = min(8, $productIds->count());
            $minAttach = min(3, $maxAttach);
            $attachCount = random_int($minAttach, $maxAttach);

            $selected = $productIds->random($attachCount)->all();
            $brand->products()->syncWithoutDetaching($selected);
        }
    }
}
