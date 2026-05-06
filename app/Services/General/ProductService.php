<?php

namespace App\Services\General;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Marvel\Database\Models\Product;

class ProductService
{
    public function paginate(Request $request)
    {
        $limit = $this->getLimit($request);
        $term = trim((string) $request->get('search', ''));

        $query = Product::query()->active()
            ->with(['shops', 'categories','variations'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        $this->applyProductFilters($query, $request);
        if ($term !== '') {
            $this->applyProductSearch($query, $term, app()->getLocale());
        }

        return $query->orderByDesc('id')->paginate($limit);
    }

    public function getBySlug($slug)
    {
        return Product::where('slug', $slug)
            ->with(['shops:id,name', 'categories:id,name'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')->first();
    }
    public function paginateFlashSales(Request $request)
    {
        $limit = $this->getLimit($request);
        $term = trim((string) $request->get('search', ''));

        $query = Product::query()
            ->with(['shops', 'categories'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        $this->applyProductFilters($query, $request);
        $this->applyFlashSaleFilter($query);

        if ($term !== '') {
            $this->applyProductSearch($query, $term, app()->getLocale());
        }

        return $query->orderByDesc('id')->paginate($limit);
    }

    public function getProductById($id)
    {
        return Product::query()->active()
            ->with(['shops:id,name', 'categories:id,name'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('id', $id)
            ->firstOrFail();
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

        $categoryId = $request->get('category');
        if ($categoryId !== null) {
            $query->whereHas('categories', function (Builder $builder) use ($categoryId) {
                $builder->where('categories.id', $categoryId);
            });
        }

        $shopId = $request->get('shop');
        if ($shopId !== null) {
            $query->whereHas('shops', function (Builder $builder) use ($shopId) {
                $builder->where('shops.id', $shopId);
            });
        }

        $filters = $request->get('filters');
        if (!empty($filters) && is_array($filters)) {
            foreach ($filters as $attributeId => $valueIds) {

                if (empty($valueIds)) {
                    continue;
                }

                $query->whereHas('variations.attributeProducts', function ($q) use ($attributeId, $valueIds) {
                    $q->whereIn('attribute_value_id', $valueIds)
                        ->whereHas('attributeValue', function ($q2) use ($attributeId) {
                            $q2->where('attribute_id', $attributeId);
                        });
                });
            }
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

    private function applyFlashSaleFilter(Builder $query): void
    {
        $now = now();

        $query->where('has_flash_sale', true)
            ->whereHas('flash_sales', function (Builder $builder) use ($now) {
                $builder->where('sale_status', true)
                    ->whereDate('start_date', '<=', $now)
                    ->whereDate('end_date', '>=', $now);
            });
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

            $builder->orWhereHas('shops', function (Builder $shopQuery) use ($term, $locale) {
                $this->applyTranslatableLike($shopQuery, 'name', $term, $locale);
            });

            $builder->orWhereHas('categories', function (Builder $categoryQuery) use ($term, $locale) {
                $this->applyTranslatableLike($categoryQuery, 'name', $term, $locale);
            });
        });
    }

    private function applyTranslatableLike(Builder $query, string $field, string $term, string $locale): void
    {

        $query->where(function ($q) use ($field, $term, $locale) {
            $q->where($field . '->' . $locale, 'like', "%$term%")
                ->orWhere($field, 'like', "%$term%");
        });
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
