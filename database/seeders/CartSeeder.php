<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\CartItem;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\User;

class CartSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $products = Product::take(2)->get();

        if (!$user || $products->isEmpty()) {
            return;
        }

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);

        CartItem::where('cart_id', $cart->id)->delete();

        foreach ($products as $product) {
            $quantity = 1;
            $price = $product->getCurrentPrice();

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $price * $quantity,
            ]);
        }
    }
}
