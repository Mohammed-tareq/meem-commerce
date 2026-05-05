<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Marvel\Database\Models\ProductVariant;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'price' => $this->faker->randomFloat(2, 10, 100),
            'sale_price' => $this->faker->randomFloat(2, 5, 90),
            'quantity' => $this->faker->numberBetween(1, 100),
            'height' => $this->faker->randomFloat(2, 1, 10),
            'width' => $this->faker->randomFloat(2, 1, 10),
            'length' => $this->faker->randomFloat(2, 1, 10),
            'weight' => $this->faker->randomFloat(2, 0.1, 5),
            'product_id' => null, // To be set when creating
        ];
    }
}
