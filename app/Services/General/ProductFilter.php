<?php

namespace App\Services\General;

use Illuminate\Database\Eloquent\Builder;
use Marvel\Database\Models\Attribute;
use Marvel\Database\Models\AttributeValue;
use Marvel\Database\Models\Brand;
use Marvel\Database\Models\Category;

class ProductFilter
{
    private function resolveIds(string $modelClass, array $values): array
    {
        return $modelClass::where(function ($q) use ($values) {
            foreach ($values as $val) {
                $q->orWhere('name', 'like', '%' . $val . '%')
                  ->orWhere('slug', 'like', '%' . $val . '%');
            }
        })->pluck('id')->toArray();
    }

    public function apply(Builder $query, array $filters): Builder
    {
        // 1. Filter by Brand
        if (!empty($filters['brand'])) {
            $brandNames = is_array($filters['brand']) ? $filters['brand'] : explode(',', $filters['brand']);
            $brandIds = $this->resolveIds(Brand::class, $brandNames);
            if (!empty($brandIds)) {
                $query->whereHas('brands', fn($q) => $q->whereIn('brands.id', $brandIds));
            }
        }

        // 2. Filter by Category
        if (!empty($filters['category'])) {
            $categoryNames = is_array($filters['category']) ? $filters['category'] : explode(',', $filters['category']);
            $categoryIds = $this->resolveIds(Category::class, $categoryNames);
            if (!empty($categoryIds)) {
                $query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds));
            }
        }

        // 3. Filter by Price
        $minPrice = $filters['minPrice'] ?? $filters['price_min'] ?? null;
        $maxPrice = $filters['maxPrice'] ?? $filters['price_max'] ?? null;
        if ($minPrice !== null || $maxPrice !== null) {
            $query->where(function ($q) use ($minPrice, $maxPrice) {
                $q->where(function ($simpleQ) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null) {
                        $simpleQ->where('products.price', '>=', $minPrice);
                    }
                    if ($maxPrice !== null) {
                        $simpleQ->where('products.price', '<=', $maxPrice);
                    }
                })->orWhereHas('variations', function ($variantQ) use ($minPrice, $maxPrice) {
                    if ($minPrice !== null) {
                        $variantQ->where('product_variants.price', '>=', $minPrice);
                    }
                    if ($maxPrice !== null) {
                        $variantQ->where('product_variants.price', '<=', $maxPrice);
                    }
                });
            });
        }

        // 4. Filter by Dimensions
        $dimensions = ['height', 'width', 'length', 'weight'];
        foreach ($dimensions as $dimension) {
            if (!empty($filters[$dimension])) {
                $values = is_array($filters[$dimension]) ? $filters[$dimension] : explode(',', $filters[$dimension]);
                $query->where(function ($q) use ($dimension, $values) {
                    $q->whereIn("products.{$dimension}", $values)
                      ->orWhereHas('variations', function ($varQ) use ($dimension, $values) {
                          $varQ->whereIn($dimension, $values);
                      });
                });
            }
        }

        // 5. Dynamic Attribute Filters
        $attributeSlugs = Attribute::pluck('slug')->toArray();

        foreach ($filters as $key => $value) {
            $lowerKey = strtolower($key);
            if (in_array($lowerKey, $attributeSlugs) && !empty($value)) {
                $attrValues = is_array($value) ? $value : explode(',', $value);

                $attrValueIds = AttributeValue::whereHas('attribute', fn($q) => $q->where('slug', $lowerKey))
                    ->where(function ($q) use ($attrValues) {
                        foreach ($attrValues as $val) {
                            $q->orWhere('value', 'like', '%' . $val . '%')
                              ->orWhere('slug', 'like', '%' . $val . '%');
                        }
                    })
                    ->pluck('id')
                    ->toArray();

                if (!empty($attrValueIds)) {
                    $query->whereHas('variations.attributeProducts', fn($q) => $q->whereIn('attribute_value_id', $attrValueIds));
                }
            }
        }

        return $query;
    }
}
