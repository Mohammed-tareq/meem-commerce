<?php

namespace App\Services\General;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Shop;

class SearchService
{
    public function search(Request $request): array
    {
        $term = trim((string) $request->get('search', ''));
        $limit = $this->getLimit($request);

        $products = $this->buildProductQuery($request, $term, app()->getLocale())
            ->paginate($limit, ['*'], 'product_page');

        $shops = $this->buildShopQuery($term, app()->getLocale())
            ->paginate($limit, ['*'], 'shop_page');

        $categories = $this->buildCategoryQuery($term, app()->getLocale())
            ->paginate($limit, ['*'], 'category_page');

        $data = [
            'products' => $products,
            'shops' => $shops,
            'categories' => $categories,
        ];

        return $data;
    }

    private function buildProductQuery(Request $request, string $term, string $locale): Builder
    {
        $query = Product::query()
            ->with(['shop:id,name,description', 'categories:id,name'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        $this->applyProductFilters($query, $request);

        if ($term !== '') {
            $this->applyProductSearch($query, $term, $locale);
        }

        return $query->orderByDesc('id');
    }

    private function applyProductFilters(Builder $query, Request $request): void
    {
        $priceMin = $request->get('price_min');
        $priceMax = $request->get('price_max');

        if ($priceMin !== null && $priceMax !== null) {
            $query->whereBetween('price', [$priceMin, $priceMax]);
        } elseif ($priceMin !== null) {
            $query->where('price', '>=', $priceMin);
        } elseif ($priceMax !== null) {
            $query->where('price', '<=', $priceMax);
        }

        $categorySlug = $request->get('category_slug');
        if ($categorySlug !== null) {
            $query->whereHas('categories', function (Builder $builder) use ($categorySlug) {
                $builder->where('categories.slug', $categorySlug);
            });
        }

        $shopSlug = $request->get('shop_slug');
        if ($shopSlug !== null) {
            $query->whereHas('shop', function (Builder $builder) use ($shopSlug) {
                $builder->where('shops.slug', $shopSlug);
            });
        }

        $ratingMin = $request->get('rating_min');
        $ratingMax = $request->get('rating_max');
        if ($ratingMin !== null || $ratingMax !== null) {
            $min = $ratingMin ?? 0;
            $max = $ratingMax ?? 5;
            $query->whereHas('reviews', function (Builder $builder) use ($min, $max) {
                $builder->whereBetween('rating', [$min, $max]);
            });
        }
    }

    private function applyProductSearch(Builder $query, string $term, string $locale): void
    {
        $query->where(function (Builder $builder) use ($term, $locale) {
            $this->applyTranslatableLike($builder, 'name', $term, $locale);

            $builder->orWhere(function (Builder $sub) use ($term, $locale) {
                $this->applyTranslatableLike($sub, 'description', $term, $locale);
            });

            if (is_numeric($term)) {
                $builder->orWhere('price', $term)
                    ->orWhere('sold_quantity', $term);
            }

            $builder->orWhereHas('reviews', function (Builder $reviewQuery) use ($term) {
                $reviewQuery->where('comment', 'like', '%' . $term . '%');
            });

            $builder->orWhereHas('shop', function (Builder $shopQuery) use ($term, $locale) {
                $this->applyTranslatableLike($shopQuery, 'name', $term, $locale);
            });

            $builder->orWhereHas('categories', function (Builder $categoryQuery) use ($term, $locale) {
                $this->applyTranslatableLike($categoryQuery, 'name', $term, $locale);
            });
        });
    }

    private function buildShopQuery(string $term, string $locale): Builder
    {
        $query = Shop::query()->with(['categories'])->withCount('products');

        if ($term !== '') {
            $query->where(function (Builder $builder) use ($term, $locale) {
                $this->applyTranslatableLike($builder, 'name', $term, $locale);
                $builder->orWhere(function (Builder $sub) use ($term, $locale) {
                    $this->applyTranslatableLike($sub, 'description', $term, $locale);
                });
            });
        }

        return $query->orderByDesc('id');
    }

    private function buildCategoryQuery(string $term, string $locale): Builder
    {
        $query = Category::query()->with(['parent', 'products'])->withCount('products');

        if ($term !== '') {
            $query->where(function (Builder $builder) use ($term, $locale) {
                $this->applyTranslatableLike($builder, 'name', $term, $locale);
                $builder->orWhere(function (Builder $sub) use ($term, $locale) {
                    $this->applyTranslatableLike($sub, 'details', $term, $locale);
                });
            });
        }

        return $query->orderByDesc('id');
    }

    private function applyTranslatableLike(Builder $query, string $field, string $term, string $locale): void
    {
        $query->where($field . '->' . $locale, 'like', '%' . $term . '%')
            ->orWhere($field, 'like', '%' . $term . '%');
    }

    private function getLimit(Request $request): int
    {
        $limit = (int) $request->get('limit', 15);
        if ($limit <= 0) {
            return 15;
        }

        return min($limit, 100);
    }
}