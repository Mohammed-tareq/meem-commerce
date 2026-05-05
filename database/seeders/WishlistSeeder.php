<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Wishlist;
use Marvel\Database\Models\ProductVariant;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        $variantIds = [
            2 => ProductVariant::where('product_id', 2)->value('id'),
            4 => ProductVariant::where('product_id', 4)->value('id'),
        ];

        $wishlistData = [
            [
                'user_id' => 3,
                'product_id' => 1,
                'product_variant_id' => null,
            ],
            [
                'user_id' => 3,
                'product_id' => 2,
                'product_variant_id' => $variantIds[2] ?? null,
            ],
            [
                'user_id' => 3,
                'product_id' => 3,
                'product_variant_id' => null,
            ],
            [
                'user_id' => 3,
                'product_id' => 4,
                'product_variant_id' => $variantIds[4] ?? null,
            ],
            [
                'user_id' => 3,
                'product_id' => 5,
                'product_variant_id' => null,
            ],
        ];

        foreach ($wishlistData as $data) {
            Wishlist::create($data);
        }
    }
}
