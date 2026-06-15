<?php

namespace App\Services\General;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Brand;
use Marvel\Database\Models\Category;
use Marvel\Database\Models\Attribute;
use Marvel\Database\Models\FlashSale;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\Review;
use Marvel\Traits\MediaManager;
use Symfony\Component\HttpKernel\Exception\HttpException;


class ProductService
{
    use MediaManager;

    public function buildFilteredBaseQuery(Request $request): Builder
    {
        $query = Product::query()->active()
            ->with(['categories', 'variations', 'brands'])
            ->withAvg(['reviews' => fn(Builder $builder) => $builder->approved()], 'rating')
            ->withCount(['reviews' => fn(Builder $builder) => $builder->approved()]);

        $this->applyProductFilters($query, $request);

        $term = trim((string) $request->get('search', ''));
        if ($term !== '') {
            $this->applyProductSearch($query, $term, app()->getLocale());
        }

        return $query;
    }

    public function paginate(Request $request)
    {
        $limit = $this->getLimit($request);
        $query = $this->buildFilteredBaseQuery($request);

        return $query->orderByDesc('id')->paginate($limit);
    }

    public function paginateFlashSales(Request $request)
    {
        $limit = $this->getLimit($request);

        $query = Product::query()
            ->with(['categories', 'variations', 'brands'])
            ->withAvg(['reviews' => fn(Builder $builder) => $builder->approved()], 'rating')
            ->withCount(['reviews' => fn(Builder $builder) => $builder->approved()]);

        $this->applyProductFilters($query, $request);
        $this->applyFlashSaleFilter($query);

        $term = trim((string) $request->get('search', ''));
        if ($term !== '') {
            $this->applyProductSearch($query, $term, app()->getLocale());
        }

        return $query->orderByDesc('id')->paginate($limit);
    }

    public function getProductBySlug($slug, $limit = 10)
    {
        $product = Product::query()
            ->active()
            ->search('slug', $slug, app()->getLocale())
            ->with([
                'categories',
                'variations',
                'brands',
                'reviews' => fn($builder) => $builder->approved()->with('user'),
            ])
            ->withAvg(['reviews' => fn($builder) => $builder->approved()], 'rating')
            ->withCount(['reviews' => fn($builder) => $builder->approved()])
            ->first();

        if (!$product) {
            return null;
        }

        $product->setRelation('related_products', $this->fetchRelated($product, $limit));

        return $product;
    }
    public function getDiscountEndingTodayOrLowStockProducts($request)
    {
        $limit = $request->query('limit', 10);
        $products = Product::query()
            ->with(['categories', 'variations', 'brands'])
            ->where('status', true)
            ->where(function ($query) {

                $query->where(function ($q) {
                    $q->where('has_discount', true)
                        ->whereDate('end_date', today());
                })
                    ->orWhere(function ($q) {
                        $q->whereBetween('stock_quantity', [1, 9]);
                    });
            })

            ->limit($limit)
            ->get();

        return $products->map(function (Product $product) {

            $product->setAttribute(
                'current_price',
                $this->moneyValue(
                    $product->price_after_discount ?? $product->price
                )
            );

            $badges = [];

            if (
                $product->has_discount &&
                $product->end_date &&
                Carbon::parse($product->end_date)->isToday()
            ) {
                $badges[] = 'discount_ending_today';
            }

            if ($product->stock_quantity >= 1 && $product->stock_quantity <= 9) {
                $badges[] = 'low_stock';
            }

            $product->setAttribute('badges', $badges);

            return $product;
        })->values();
    }
    public function getFlashSalesAndHereProductsByQtySet($request)
    {
        $qty = $request->query('limit', 5);
        $start_date = $request->query('start_date', '');
        $end_date = $request->query('end_date', '');

        $flashSales = FlashSale::query()->valid()
            ->when($start_date, function ($query) use ($start_date) {
                $query->where('created_at', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->where('created_at', '<=', $end_date);
            })
            ->with([
                'products' => function ($query) use ($qty) {
                    $query->limit($qty);
                }
            ])->get()
            ->pluck('products')
            ->flatten();

        return $flashSales;
    }
    public function getFlashSaleProductsEndingThisWeek($request)
    {
        $limit = $request->query('limit', 10);
        $weekEnd = now()->endOfWeek();

        $products = Product::query()
            ->with(['categories', 'variations', 'brands'])
            ->select([
                'id',
                'name',
                'slug',
                'price',
                'quantity',
                'has_flash_sale',
                'has_discount',
                'discount_type',
                'discount_amount',
                'discount_status',
                'start_date',
                'end_date',
                'price_after_discount',
                'price_after_flash_sale',
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
            ->limit($limit)
            ->get();

        return $products->map(function (Product $product) {
            $product->setAttribute('current_price', $this->moneyValue($product->price_after_flash_sale ?? $product->price_after_discount ?? $product->price ?? null));

            return $product;
        })->values();
    }
    public function getFlashSaleProductsEndingToday($request)
    {
        $limit = $request->query('limit', 10);
        $today = today();

        $products = Product::query()
            ->with(['categories', 'variations', 'brands'])
            ->select([
                'id',
                'name',
                'slug',
                'price',
                'quantity',
                'has_flash_sale',
                'has_discount',
                'discount_type',
                'discount_amount',
                'discount_status',
                'start_date',
                'end_date',
                'price_after_discount',
                'price_after_flash_sale',
            ])
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('has_flash_sale', true)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('flash_sale_products')
                    ->join('flash_sales', 'flash_sale_products.flash_sale_id', '=', 'flash_sales.id')
                    ->whereColumn('flash_sale_products.product_id', 'products.id')
                    ->whereNull('flash_sales.deleted_at')
                    ->where('flash_sales.status', true)
                    ->whereNotNull('flash_sales.end_date')
                    ->whereDate('flash_sales.end_date', today());
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $products->map(function (Product $product) {
            $product->setAttribute('current_price', $this->moneyValue($product->price_after_flash_sale ?? $product->price_after_discount ?? $product->price ?? null));

            return $product;
        })->values();
    }
    public function getAllDiscountProducts($request)
    {
        $limit = $request->query('limit', 10);
        $products = Product::query()
            ->select([
                'id',
                'name',
                'slug',
                'price',
                'quantity',
                'has_flash_sale',
                'has_discount',
                'discount_type',
                'discount_amount',
                'discount_status',
                'start_date',
                'end_date',
                'price_after_discount',
                'price_after_flash_sale',
            ])
            ->with(['reviews', 'media', 'categories', 'variations', 'brands'])
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('has_discount', true)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return $products->map(function (Product $product) {
            $product->setAttribute('current_price', $this->moneyValue($product->price_after_discount ?? $product->price ?? null));

            return $product;
        })->filter(fn(Product $product) => $product->isDiscountActive())->values();
    }

    public function getBrandsProductsByQtySet($request)
    {
        $qty = $request->query('limit', 10);
        // $qtyBrand = $request->query('limit_brand', 10);
        $start_date = $request->query('start_date', '');
        $end_date   = $request->query('end_date', '');

        $brands = Brand::active()
            ->when(!empty($start_date), function ($query) use ($start_date) {
                $query->where('created_at', '>=', $start_date);
            })
            ->when(!empty($end_date), function ($query) use ($end_date) {
                $query->where('created_at', '<=', $end_date);
            })
            ->with(['products' => function ($query) use ($qty) {
                $query->with(['categories', 'variations', 'brands'])->limit($qty);
            }])
            ->get()
            ->pluck('products')
            ->flatten();

        return $brands;
    }
    public function getNewArrivals($request)
    {
        $limit = $request->get('limit', 10);
        $products = Product::query()
            ->select([
                'id',
                'name',
                'slug',
                'price',
                'quantity',
                'has_flash_sale',
                'has_discount',
                'discount_type',
                'discount_amount',
                'discount_status',
                'start_date',
                'end_date',
                'price_after_discount',
                'price_after_flash_sale',
            ])
            ->with(['reviews', 'media', 'categories', 'variations', 'brands'])
            ->whereNull('deleted_at')
            ->where('status', true)
            ->where('has_flash_sale', false)
            ->whereDate('created_at', '>=', now()->subDays(15))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $products->map(function (Product $product) {
            $product->setAttribute('current_price', $this->moneyValue($product->price_after_discount ?? $product->price ?? null));

            return $product;
        })->values();
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

    public function getBestProductSales($request)
    {
        $limit = $request->get('limit', 10);

        return Product::query()
            ->active()
            ->with(['categories', 'variations', 'brands'])
            ->orderByDesc('sold_quantity')
            ->limit($limit)
            ->get();
    }

    public function getProductForParentCategory($request)
    {

        $limit = $request->integer('limit', 10);
        $ParentCategories = Category::query()->whereNull('parent_id')->pluck('id');
        return Product::query()
            ->active()
            ->with(['categories', 'variations', 'brands'])
            ->whereHas('categories', function (Builder $query) use ($ParentCategories) {
                $query->whereIn('categories.id', $ParentCategories);
            })
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    private function fetchRelated(Product $product, int $limit = 10)
    {
        $categories = $product->categories->pluck('id');

        if ($categories->isEmpty()) {
            return collect();
        }

        return Product::query()
            ->active()
            ->with(['categories', 'variations', 'brands'])
            ->whereHas('categories', function (Builder $query) use ($categories) {
                $query->whereIn('categories.id', $categories);
            })
            ->where('id', '!=', $product->id)
            ->limit($limit)
            ->get();
    }


    private function applyProductFilters(Builder $query, Request $request): void
    {
        $query->filter($request->all());

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

    public function getLimit(Request $request): int
    {
        $limit = (int) $request->get('limit', 15);
        if ($limit <= 0) {
            return 15;
        }

        return min($limit, 100);
    }

    public function getDynamicFilters(Builder $query): array
    {
        $filters = [];

        $filteredIds = (clone $query)->select('products.id')->pluck('id');

        if ($filteredIds->isEmpty()) {
            return $filters;
        }

        $displayLabels = [
            'brand'    => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
            'category' => ['en' => 'Category', 'ar' => 'الفئة'],
            'height'   => ['en' => 'Height', 'ar' => 'الارتفاع'],
            'width'    => ['en' => 'Width', 'ar' => 'العرض'],
            'length'   => ['en' => 'Length', 'ar' => 'الطول'],
            'weight'   => ['en' => 'Weight', 'ar' => 'الوزن'],
        ];

        $brands = Brand::active()
            ->whereHas('products', fn($q) => $q->whereIn('products.id', $filteredIds))
            ->get()
            ->map(fn($b) => $b->name)
            ->filter()
            ->values()
            ->toArray();
        if (!empty($brands)) {
            $filters[] = [
                'display' => $displayLabels['brand'][app()->getLocale()],
                'key'     => 'brand',
                'data'    => $brands,
            ];
        }

        $categories = Category::active()
            ->whereHas('products', fn($q) => $q->whereIn('products.id', $filteredIds))
            ->get()
            ->map(fn($c) => $c->name)
            ->filter()
            ->values()
            ->toArray();
        if (!empty($categories)) {
            $filters[] = [
                'display' => $displayLabels['category'][app()->getLocale()],
                'key'     => 'category',
                'data'    => $categories,
            ];
        }

        $dimensions = ['height', 'width', 'length', 'weight'];
        foreach ($dimensions as $column) {
            $productValues = (clone $query)
                ->whereNotNull($column)->where($column, '!=', '')
                ->distinct()->pluck($column)->toArray();

            $variantValues = DB::table('product_variants')
                ->whereIn('product_id', $filteredIds)
                ->whereNotNull($column)->where($column, '!=', '')
                ->distinct()->pluck($column)->toArray();

            $values = array_values(array_unique(array_merge($productValues, $variantValues)));
            $values = array_map('strval', $values);
            $values = array_values(array_filter($values, fn($v) => $v !== ''));
            sort($values, SORT_NATURAL | SORT_FLAG_CASE);

            if (!empty($values)) {
                $filters[] = [
                    'display' => $displayLabels[$column][app()->getLocale()],
                    'key'     => $column,
                    'data'    => $values,
                ];
            }
        }

        $attributes = Attribute::with(['values' => fn($q) => $q->whereHas('productVariants', fn($pq) => $pq->whereIn('product_id', $filteredIds))])->get();
        foreach ($attributes as $attribute) {
            $values = $attribute->values->map(fn($v) => $v->value)->filter()->values()->toArray();
            if (!empty($values)) {
                $filters[] = [
                    'display' => $attribute->getTranslations('name'),
                    'key'     => $attribute->slug,
                    'data'    => $values,
                ];
            }
        }

        return $filters;
    }

    private function moneyValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }
}
