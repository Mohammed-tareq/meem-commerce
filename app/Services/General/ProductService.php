<?php

namespace App\Services\General;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Review;
use Marvel\Traits\MediaManager;
use Symfony\Component\HttpKernel\Exception\HttpException;


class ProductService
{
    use MediaManager;
    public function paginate(Request $request)
    {
        $limit = $this->getLimit($request);
        $term = trim((string) $request->get('search', ''));

        $query = Product::query()->active()
            ->with(['shops', 'categories', 'variations', 'media'])
            ->withAvg(['reviews' => fn (Builder $builder) => $builder->approved()], 'rating')
            ->withCount(['reviews' => fn (Builder $builder) => $builder->approved()]);

        $this->applyProductFilters($query, $request);
        if ($term !== '') {
            $this->applyProductSearch($query, $term, app()->getLocale());
        }

        return $query->orderByDesc('id')->paginate($limit);
    }


    public function paginateFlashSales(Request $request)
    {
        $limit = $this->getLimit($request);
        $term = trim((string) $request->get('search', ''));

        $query = Product::query()
            ->with(['shops', 'categories', 'media'])
            ->withAvg(['reviews' => fn (Builder $builder) => $builder->approved()], 'rating')
            ->withCount(['reviews' => fn (Builder $builder) => $builder->approved()]);

        $this->applyProductFilters($query, $request);
        $this->applyFlashSaleFilter($query);

        if ($term !== '') {
            $this->applyProductSearch($query, $term, app()->getLocale());
        }

        return $query->orderByDesc('id')->paginate($limit);
    }

    public function getProductById($id, $limit = 10)
    {
        $product = Product::query()
            ->active()
            ->with([
                'categories',
                'variations',
                'media',
                'reviews' => fn (Builder $builder) => $builder->approved()->with('user'),
            ])
            ->withAvg(['reviews' => fn (Builder $builder) => $builder->approved()], 'rating')
            ->withCount(['reviews' => fn (Builder $builder) => $builder->approved()])
            ->find($id);

        if (!$product) {
            return null;
        }

        $product->setRelation('related_products', $this->fetchRelated($product, $limit));

        return $product;
    }



    public function addProductReview($request, $id)
    {
        try {
            DB::beginTransaction();
            $product = Product::find($id);
            if (!$product) {
                return null;
            }
            $reviewData = $request->only(['rating', 'comment']);
            $reviewData['user_id'] = auth()->id();
            $reviewData['product_id'] = $id;

            $review = $product->reviews()->create($reviewData);
            if ($request->has('images')) {
                if (!$this->uploadImages($request, 'images', $review, 'reviews', 'reviews')) {
                    throw new HttpException(422, 'Logo upload failed, please check the file format or size.');
                }
            }
            DB::commit();
            return $review;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function updateProductReview($request, $id)
    {
        try {
            DB::beginTransaction();
            $review = Review::find($id);
            if (!$review || $review->user_id !== auth()->id()) {
                return null;
            }

            $reviewData = $request->only(['rating', 'comment']);

            $review->update($reviewData);
            if ($request->has('images')) {
                if (!$this->uploadImages($request, 'images', $review->fresh(), 'reviews', 'reviews')) {
                    throw new HttpException(422, 'Logo upload failed, please check the file format or size.');
                }
            }
            DB::commit();
            return $review;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function fetchRelated(Product $product, int $limit = 10)
    {
        $categories = $product->categories->pluck('id');

        if ($categories->isEmpty()) {
            return collect();
        }

        return Product::query()
            ->active()
            ->with(['categories', 'variations', 'media'])
            ->whereHas('categories', function (Builder $query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            })
            ->where('id', '!=', $product->id)
            ->limit($limit)
            ->get();
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

        $filters = $request->get('attributes', []);

        if (is_array($filters) && count($filters) > 0) {
            $locale = app()->getLocale();
            foreach ($filters as $attributeName => $valueNames) {
                if (empty($valueNames)) {
                    continue;
                }
                $valueNames = is_array($valueNames) ? $valueNames : [$valueNames];
                $query->whereHas('variations.attributeProducts.attributeValue', function (Builder $builder) use ($attributeName, $valueNames, $locale) {
                    $builder->where(function (Builder $q) use ($attributeName, $valueNames, $locale) {
                        $q->whereHas('attribute', function (Builder $attributeBuilder) use ($attributeName, $locale) {
                            $this->applyTranslatableLike($attributeBuilder, 'name', $attributeName, $locale);
                        });
                        $q->where(function (Builder $valueBuilder) use ($valueNames, $locale) {
                            foreach ($valueNames as $valueName) {
                                $valueBuilder->orWhere(function (Builder $valueQuery) use ($valueName, $locale) {
                                    $this->applyTranslatableLike($valueQuery, 'value', $valueName, $locale);
                                });
                            }
                        });
                    });
                });
            }
        }

        $this->applyDimensionFilters($query, $request);

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

    private function applyDimensionFilters(Builder $query, Request $request): void
    {
        $dimensions = [
            'height' => ['height_min', 'height_max'],
            'width' => ['width_min', 'width_max'],
            'length' => ['length_min', 'length_max'],
            'weight' => ['weight_min', 'weight_max'],
        ];

        foreach ($dimensions as $column => [$minKey, $maxKey]) {
            $this->applyDimensionRange(
                $query,
                $column,
                $request->get($minKey),
                $request->get($maxKey)
            );
        }
    }

    private function applyDimensionRange(Builder $query, string $column, mixed $min, mixed $max): void
    {
        $allowed = ['height', 'width', 'length', 'weight'];
        if (!in_array($column, $allowed, true)) {
            return;
        }

        $hasMin = $min !== null && $min !== '';
        $hasMax = $max !== null && $max !== '';

        if (!$hasMin && !$hasMax) {
            return;
        }

        $minValue = $hasMin ? (float) $min : null;
        $maxValue = $hasMax ? (float) $max : null;

        $productNumericSql = "CAST(REGEXP_REPLACE(COALESCE(products.{$column}, ''), '[^0-9.]', '') AS DECIMAL(12,4))";

        $query->where(function (Builder $outer) use ($column, $minValue, $maxValue, $productNumericSql, $hasMin, $hasMax) {
            $outer->where(function (Builder $productQuery) use ($productNumericSql, $minValue, $maxValue, $hasMin, $hasMax) {
                if ($hasMin) {
                    $productQuery->whereRaw("{$productNumericSql} >= ?", [$minValue]);
                }
                if ($hasMax) {
                    $productQuery->whereRaw("{$productNumericSql} <= ?", [$maxValue]);
                }
            })->orWhereHas('variations', function (Builder $variantQuery) use ($column, $minValue, $maxValue, $hasMin, $hasMax) {
                if ($hasMin) {
                    $variantQuery->where($column, '>=', $minValue);
                }
                if ($hasMax) {
                    $variantQuery->where($column, '<=', $maxValue);
                }
            });
        });
    }

    private function applyFlashSaleFilter(Builder $query): void
    {
        $now = now();

        $query->where('has_flash_sale', true)
            ->whereHas('flash_sales', function (Builder $builder) use ($now) {
                $builder->where('status', true)
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

            foreach (['height', 'width', 'length', 'weight'] as $dimension) {
                $builder->orWhere($dimension, 'like', '%' . $term . '%');
            }

            $builder->orWhereHas('variations', function (Builder $variantQuery) use ($term) {
                $variantQuery->where(function (Builder $dimensions) use ($term) {
                    foreach (['height', 'width', 'length', 'weight'] as $dimension) {
                        $dimensions->orWhere($dimension, 'like', '%' . $term . '%');
                    }
                });
            });

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
