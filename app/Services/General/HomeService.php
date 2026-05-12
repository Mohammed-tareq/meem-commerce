<?php

namespace App\Services\General;

use App\Http\Resources\Category\CategoryHomeResource;
use App\Http\Resources\Category\CategoryWithChildNameResource;
use App\Http\Resources\Product\ProductMiniResource ;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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

        return Cache::remember("home_data:parent:{$parentCategoryId}", 60, function () use ($parentCategoryId) {
            $categoryTree = $this->getCategoryTree($parentCategoryId);
            $categoriesWithChildren = $this->getCategories();

            return [
                'nav-bar'     => CategoryWithChildNameResource::collection($this->getCategoryWithChildren()),
                'active_sliders' => SliderResource::collection($this->getActiveSliders()),
                'active_banners' => BannerResource::collection($this->getActiveBanners()),
                'best_categories' => CategoryHomeResource::collection($categoriesWithChildren),
                'parent_categories' => CategoryHomeResource::collection($categoryTree),
                'discount_products_end_today' => ProductMiniResource::collection($this->getDiscountEndingTodayOrLowStockProducts()),
                'flash_sales' => FlashSaleResource::collection($this->getFlashSales(9)),
                'flash_sale_products' => ProductMiniResource::collection($this->getFlashSaleProductsEndingThisWeek()),
                'weekly_parent_categories' => CategoryHomeResource::collection($categoryTree),
                'weekly_products' => ProductMiniResource::collection($this->getWeeklyCategoryProducts($categoryTree)),
                'all_discount_products' => ProductMiniResource::collection($this->getAllDiscountProducts()),
                'flash_sales_after_9' => FlashSaleResource::collection($this->getFlashSales(9, 9)),
            ];
        });
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
        return Product::query()->active()
            ->where('has_discount', true)
            ->where('has_flash_sale', false)
            ->where(function (Builder $query) {
                $query->whereDate('end_date', today())
                    ->orWhere('quantity', '<', 10);
            })
            ->orderByDesc('id')
            ->limit(25)
            ->get()
            ->filter(fn($product) => $product->isDiscountActive());
    }

    public function getFlashSaleProductsEndingThisWeek(): Collection
    {
        $weekEnd = now()->endOfWeek();

        return Product::query()->active()
            ->where('has_flash_sale', true)
            ->whereHas('flash_sales', function ($query) use ($weekEnd) {
                $query->valid()
                    ->whereNotNull('end_date')
                    ->whereBetween('end_date', [today(), $weekEnd]);
            })
            ->orderByDesc('id')
            ->get();
    }

    public function getWeeklyCategoryProducts(Collection $categoryTree, int $productLimit = 10): Collection
    {
        $categoryIds = $categoryTree->pluck('id')->all();

        return Product::query()->active()
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->where('has_discount', true)
            ->orderByDesc('id')
            ->limit($productLimit)
            ->get()
            ->filter(fn(Product $product) => $product->isDiscountActive());
    }

    public function getAllDiscountProducts(): Collection
    {
        return Product::query()->active()
            ->where('has_discount', true)
            ->with(['categories'])
            ->orderByDesc('id')
            ->get()
            ->filter(fn($product) => $product->isDiscountActive());
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

    // private function getCategoryWithChildren(): Collection
    // {
    //     return Category::query()
    //         ->active()
    //         ->whereNull('parent_id')
    //         ->with(['children' => function ($query) {
    //             $query->active()
    //                 ->select('id', 'parent_id', 'name')
    //                 ->with(['children' => function ($query) {
    //                     $query->active()->select('id', 'parent_id', 'name');
    //                 }]);
    //         }])
    //         ->get();
    // }
    private function getCategoryWithChildren(): Collection
{
    return Category::query()
        ->active()
        ->whereNull('parent_id')
        ->with(['children' => function ($query) {
            $query->active()
                ->select('id', 'parent_id', 'name', 'slug')
                ->with(['children' => function ($query) {
                    $query->active()->select('id', 'parent_id', 'name', 'slug');
                }]);
        }])
        ->get();
}




    private function roundMoney($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
