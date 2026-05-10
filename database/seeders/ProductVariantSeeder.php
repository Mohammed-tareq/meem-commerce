<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Marvel\Database\Models\Attribute;
use Marvel\Database\Models\AttributeProduct;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\ProductVariant;
use Marvel\Services\Pricing\ProductPricingService;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $pricingService = app(ProductPricingService::class);
        $attributes = Attribute::with('values')->get();

        $colorValues = $this->valuesForAttribute($attributes, 'Color');
        $sizeValues = $this->valuesForAttribute($attributes, 'Size');

        $products = Product::whereIn('id', [1, 2, 3, 4, 5])->with('flash_sales')->get();

        foreach ($products as $index => $product) {
            $basePrice = (float) $product->price;
            $flashSale = $product->has_flash_sale ? $pricingService->resolveActiveFlashSale($product) : null;

            $variants = [
                [
                    'price' => $basePrice,
                    'quantity' => max(5, (int) $product->quantity),
                    'height' => $product->height,
                    'width' => $product->width,
                    'length' => $product->length,
                    'weight' => $product->weight,
                    'attributes' => [
                        $colorValues[$index % count($colorValues)] ?? null,
                        $sizeValues[$index % count($sizeValues)] ?? null,
                    ],
                ],
                [
                    'price' => $basePrice + 15,
                    'quantity' => max(3, (int) $product->quantity - 2),
                    'height' => $product->height,
                    'width' => $product->width,
                    'length' => $product->length,
                    'weight' => $product->weight,
                    'attributes' => [
                        $colorValues[($index + 1) % count($colorValues)] ?? null,
                        $sizeValues[($index + 1) % count($sizeValues)] ?? null,
                    ],
                ],
            ];

            foreach ($variants as $variantData) {
                $attributeValues = array_values(array_filter($variantData['attributes']));

                $variant = ProductVariant::create([
                    'price' => $variantData['price'],
                    'sale_price' => $pricingService->calculateVariantSalePrice($product, $variantData, $flashSale),
                    'quantity' => $variantData['quantity'],
                    'height' => $variantData['height'],
                    'width' => $variantData['width'],
                    'length' => $variantData['length'],
                    'weight' => $variantData['weight'],
                    'product_id' => $product->id,
                ]);

                foreach ($attributeValues as $attributeValue) {
                    AttributeProduct::create([
                        'product_variant_id' => $variant->id,
                        'attribute_value_id' => $attributeValue->id,
                    ]);
                }
            }
        }
    }

    private function valuesForAttribute($attributes, string $attributeName)
    {
        $attribute = $attributes->first(function ($item) use ($attributeName) {
            return strtolower((string) $item->getTranslation('name', 'en')) === strtolower($attributeName);
        });

        return $attribute ? $attribute->values->values() : collect();
    }
}
