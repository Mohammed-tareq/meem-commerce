<?php

namespace App\Services\General;

use App\Http\Resources\Category\CategoryHomeResource;
use App\Http\Resources\Category\CategoryWithChildNameResource;
use App\Http\Resources\Product\ProductMiniResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Banner;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Slider;
use Marvel\Http\Resources\BannerResource;
use Marvel\Http\Resources\FlashSaleResource;
use Marvel\Http\Resources\SliderResource;

class HomeService
{
    public function getHomeData(?int $parentCategoryId = null)
    {
        $parentCategoryId = $parentCategoryId ?: 1;

        $categoryTree = Cache::remember("home_data:parent:{$parentCategoryId}:category-tree", 60, function () use ($parentCategoryId) {
            return $this->getCategoryTree($parentCategoryId);
        });

        $categoriesWithChildren = Cache::remember("home_data:parent:{$parentCategoryId}:categories-with-children", 60, function () {
            return $this->getCategories();
        });

        return [
            'nav-bar' => Cache::remember("home-nav-bar", 60, function () {
                return CategoryWithChildNameResource::collection($this->getCategoryWithChildren());
            }),
            'active_sliders' => Cache::remember("home-active-sliders", 60, function () {
                return SliderResource::collection($this->getActiveSliders());
            }),
            'active_banners' => Cache::remember("home-active-banners", 60, function () {
                return BannerResource::collection($this->getActiveBanners());
            }),
            'best_categories' => Cache::remember("home-best-categories", 60, function () use ($categoriesWithChildren) {
                return CategoryHomeResource::collection($categoriesWithChildren);
            }),
            'parent_categories' => Cache::remember("home-parent-categories", 60, function () use ($categoryTree) {
                return CategoryHomeResource::collection($categoryTree);
            }),
            'discount_products_end_today' => Cache::remember("home-discount-products-end-today", 60, function () {
                return ProductMiniResource::collection($this->getDiscountEndingTodayOrLowStockProducts());
            }),
            'flash_sales' => Cache::remember("home-flash-sales", 60, function () {
                return FlashSaleResource::collection($this->getFlashSales(9));
            }),
            'flash_sale_products' => Cache::remember("home-flash-sale-products", 60, function () {
                return ProductMiniResource::collection($this->getFlashSaleProductsEndingThisWeek());
            }),
            'weekly_parent_categories' => Cache::remember("home-weekly-parent-categories", 60, function () use ($categoryTree) {
                return CategoryHomeResource::collection($categoryTree);
            }),
            'weekly_products' => Cache::remember("home-weekly-products", 60, function () use ($categoryTree) {
                return ProductMiniResource::collection($this->getWeeklyCategoryProducts($categoryTree));
            }),
            'all_discount_products' => Cache::remember("home-all-discount-products", 60, function () {
                return ProductMiniResource::collection($this->getAllDiscountProducts());
            }),
            'flash_sales_after_9' => Cache::remember("home-flash-sales-after-9", 60, function () {
                return FlashSaleResource::collection($this->getFlashSales(9, 9));
            }),
        ];
    }

    public function getActiveSliders(): Collection
    {
        return Slider::active()->ordered()->get();
    }

    public function getActiveBanners(): Collection
    {
        return Banner::active()->ordered()->get();
    }

    public function getFlashSales(int $limit, $after = null): Collection
    {
        return FlashSale::query()->valid()
            ->when($after, function ($query, $after) {
                $query->where('id', '>', $after);
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function getDiscountEndingTodayOrLowStockProducts(): Collection
    {
        $products = DB::table('products')
            ->select([
                'id', 'name', 'slug', 'description', 'price', 'product_type', 'sku', 'quantity',
                'sold_quantity', 'in_stock', 'status', 'height', 'width', 'length', 'weight',
                'has_flash_sale', 'has_discount', 'pieces', 'banner_id', 'discount_type',
                'discount_amount', 'discount_status', 'start_date', 'end_date',
                'price_after_discount', 'price_after_flash_sale',
            ])
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('has_discount', true)
            ->where('has_flash_sale', false)
            ->where(function ($query) {
                $query->whereDate('end_date', today())
                    ->orWhere('quantity', '<', 10);
            })
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        return $products->map(function ($product) {
            $model = new Product();
            $attributes = (array) $product;
            $attributes['current_price'] = $this->moneyValue($product->price_after_discount ?? $product->price ?? null);
            $model->setRawAttributes($attributes, true);

            return $model;
        })->filter(fn(Product $product) => $product->isDiscountActive())->values();
    }

    public function getFlashSaleProductsEndingThisWeek(): Collection
    {
        $weekEnd = now()->endOfWeek();

        $products = DB::table('products')
            ->select([
                'id', 'name', 'slug', 'description', 'price', 'product_type', 'sku', 'quantity',
                'sold_quantity', 'in_stock', 'status', 'height', 'width', 'length', 'weight',
                'has_flash_sale', 'has_discount', 'pieces', 'banner_id', 'discount_type',
                'discount_amount', 'discount_status', 'start_date', 'end_date',
                'price_after_discount', 'price_after_flash_sale',
            ])
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('has_flash_sale', true)
            ->whereExists(function ($query) use ($weekEnd) {
                $query->select(DB::raw(1))
                    ->from('flash_sale_products')
                    ->join('flash_sales', 'flash_sale_products.flash_sale_id', '=', 'flash_sales.id')
                    ->whereColumn('flash_sale_products.product_id', 'products.id')
                    ->whereNull('flash_sales.deleted_at')
                    ->where('flash_sales.status', true)
                    ->whereNotNull('flash_sales.end_date')
                    ->whereBetween('flash_sales.end_date', [today(), $weekEnd]);
            })
            ->orderByDesc('id')
            ->get();

        return $products->map(function ($product) {
            $model = new Product();
            $attributes = (array) $product;
            $attributes['current_price'] = $this->moneyValue($product->price_after_flash_sale ?? $product->price_after_discount ?? $product->price ?? null);
            $model->setRawAttributes($attributes, true);

            return $model;
        })->values();
    }

    public function getWeeklyCategoryProducts(Collection $categoryTree, int $productLimit = 10): Collection
    {
        $categoryIds = $categoryTree->pluck('id')->all();

        $products = DB::table('products')
            ->select([
                'id', 'name', 'slug', 'description', 'price', 'product_type', 'sku', 'quantity',
                'sold_quantity', 'in_stock', 'status', 'height', 'width', 'length', 'weight',
                'has_flash_sale', 'has_discount', 'pieces', 'banner_id', 'discount_type',
                'discount_amount', 'discount_status', 'start_date', 'end_date',
                'price_after_discount', 'price_after_flash_sale',
            ])
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('has_discount', true)
            ->whereExists(function ($query) use ($categoryIds) {
                $query->select(DB::raw(1))
                    ->from('category_product')
                    ->whereColumn('category_product.product_id', 'products.id')
                    ->whereIn('category_product.category_id', $categoryIds);
            })
            ->orderByDesc('id')
            ->limit($productLimit)
            ->get();

        return $products->map(function ($product) {
            $model = new Product();
            $attributes = (array) $product;
            $attributes['current_price'] = $this->moneyValue($product->price_after_discount ?? $product->price ?? null);
            $model->setRawAttributes($attributes, true);

            return $model;
        })->filter(fn(Product $product) => $product->isDiscountActive())->values();
    }

    public function getAllDiscountProducts(): Collection
    {
        $products = DB::table('products')
            ->select([
                'id', 'name', 'slug', 'description', 'price', 'product_type', 'sku', 'quantity',
                'sold_quantity', 'in_stock', 'status', 'height', 'width', 'length', 'weight',
                'has_flash_sale', 'has_discount', 'pieces', 'banner_id', 'discount_type',
                'discount_amount', 'discount_status', 'start_date', 'end_date',
                'price_after_discount', 'price_after_flash_sale',
            ])
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('has_discount', true)
            ->orderByDesc('id')
            ->get();

        return $products->map(function ($product) {
            $model = new Product();
            $attributes = (array) $product;
            $attributes['current_price'] = $this->moneyValue($product->price_after_discount ?? $product->price ?? null);
            $model->setRawAttributes($attributes, true);

            return $model;
        })->filter(fn(Product $product) => $product->isDiscountActive())->values();
    }

    private function getCategoryTree($id = 1): Collection
    {
        $parent = Category::query()->active()
            ->whereNull('parent_id')
            ->where('id', '=', $id)
            ->with('children')
            ->first();

        if (!$parent) {
            return collect();
        }
        return $parent->children()->active()->limit(5)->get();
    }


    private function getCategories(): Collection
    {
        $categories = Category::query()->active()
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit(20)
            ->get();

        return $categories;
    }


    private function getCategoryWithChildren(): Collection
    {
        return Category::query()
            ->active()
            ->with(['children' => function ($query) {
                $query->active()->select('id', 'parent_id', 'name', 'slug');
            }])
            ->get();
    }

    private function moneyValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

}
