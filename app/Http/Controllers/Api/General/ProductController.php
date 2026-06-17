<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductMiniResource;
use App\Http\Resources\Product\ProductResource;
use App\Services\General\ProductEngine\ProductStrategyResolver;
use App\Services\General\ProductService;
use Illuminate\Http\Request;
use Marvel\Database\Models\Category;
use Marvel\Http\Requests\ReviewCreateRequest;
use Marvel\Http\Requests\ReviewUpdateRequest;
use Marvel\Http\Resources\product\ProductCollectionMini;
use Marvel\Traits\ApiResponse;

class ProductController extends Controller
{

    use ApiResponse;
    private ProductService $productService;
    protected $productStrategyResolver;

    public function __construct(ProductService $productService, ProductStrategyResolver $productStrategyResolver)
    {
        $this->productService = $productService;
        $this->productStrategyResolver = $productStrategyResolver;
    }

    // public function index(Request $request)
    // {
    //     $type = $request->query('type', '');
    //     if (!empty($type)) {
    //         $cacheKey = 'products_type_' . $type . '_' . md5(json_encode($request->all()) . '_' . app()->getLocale());

    //         $responseData = Cache::remember($cacheKey, 60, function () use ($request, $type) {
    //             $handler = $this->productStrategyResolver->resolve($type);
    //             $data = $handler->getProducts($request);

    //             $productIds = $data instanceof \Illuminate\Pagination\LengthAwarePaginator
    //                 ? $data->getCollection()->pluck('id')
    //                 : $data->pluck('id');

    //             $filters = [];
    //             if ($productIds->isNotEmpty()) {
    //                 $query = \Marvel\Database\Models\Product::query()->whereIn('id', $productIds);
    //                 $filters = $this->productService->getDynamicFilters($query);
    //             }

    //             if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
    //                 $collection = new ProductCollectionMini($data);
    //                 $result = $collection->toArray($request);
    //             } else {
    //                 $total = $data->count();
    //                 $result = [
    //                     'data' => ProductMiniResource::collection($data),
    //                     'links' => [
    //                         'current_page' => 1,
    //                         'from' => $total > 0 ? 1 : null,
    //                         'to' => $total,
    //                         'last_page' => 1,
    //                         'path' => $request->url(),
    //                         'per_page' => $total,
    //                         'total' => $total,
    //                         'next_page_url' => null,
    //                         'prev_page_url' => null,
    //                         'last_page_url' => $request->fullUrlWithQuery(['page' => 1]),
    //                         'first_page_url' => $request->fullUrlWithQuery(['page' => 1]),
    //                     ],
    //                 ];
    //             }

    //             $result['filters'] = $filters;
    //             return $result;
    //         });

    //         $responseData['category'] = $this->resolveCategoryForResponse($request);
    //         return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $responseData);
    //     }

    //     $cacheKey = 'products_index_' . md5(json_encode($request->all()) . '_' . app()->getLocale());

    //     $responseData = Cache::remember($cacheKey, 60, function () use ($request) {
    //         $query = $this->productService->buildFilteredBaseQuery($request);
    //         $filters = $this->productService->getDynamicFilters(clone $query);
    //         $orderPrice = $request->query('order_price');
    //         if (in_array($orderPrice, ['asc', 'desc'])) {
    //             $query->orderBy('price', $orderPrice);
    //         }
    //         $data = $query->orderByDesc('id')->paginate($this->productService->getLimit($request));
    //         $collection = new ProductCollectionMini($data);
    //         $collectionArray = $collection->toArray($request);
    //         $collectionArray['filters'] = $filters;
    //         return $collectionArray;
    //     });

    //     $responseData['category'] = $this->resolveCategoryForResponse($request);

    //     return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $responseData);
    // }

    public function index(Request $request)
    {
        $type = $request->query('type', '');
        $order = $request->query('order', 'desc');
        if (!empty($type)) {
            $handler = $this->productStrategyResolver->resolve($type);
            $data = $handler->getProducts($request);

            $productIds = $data instanceof \Illuminate\Pagination\LengthAwarePaginator
                ? $data->getCollection()->pluck('id')
                : $data->pluck('id');

            $filters = [];
            if ($productIds->isNotEmpty()) {
                $query = \Marvel\Database\Models\Product::query()->whereIn('id', $productIds);
                $filters = $this->productService->getDynamicFilters($query);
            }

            if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                $collection = new ProductCollectionMini($data);
                $responseData = $collection->toArray($request);
            } else {
                $total = $data->count();
                $responseData = [
                    'data' => ProductMiniResource::collection($data),
                    'links' => [
                        'current_page' => 1,
                        'from' => $total > 0 ? 1 : null,
                        'to' => $total,
                        'last_page' => 1,
                        'path' => $request->url(),
                        'per_page' => $total,
                        'total' => $total,
                        'next_page_url' => null,
                        'prev_page_url' => null,
                        'last_page_url' => $request->fullUrlWithQuery(['page' => 1]),
                        'first_page_url' => $request->fullUrlWithQuery(['page' => 1]),
                    ],
                ];
            }

            $responseData['filters'] = $filters;
            $responseData['categories'] = $this->getCollectionCategories($productIds);

            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $responseData);
        }

        $scoutQuery = $this->productService->buildScoutSearchQuery($request);

        if ($scoutQuery !== null) {
            $data = $scoutQuery->orderBy('id', $order)->paginate($this->productService->getLimit($request));
            $filters = $this->productService->getDynamicFilters(clone $scoutQuery);
        } else {
            $query = $this->productService->buildFilteredBaseQuery($request);
            $filters = $this->productService->getDynamicFilters(clone $query);
            $orderPrice = $request->query('order_price');
            if (in_array($orderPrice, ['asc', 'desc'])) {
                $query->orderBy('price', $orderPrice);
            }
            $data = $query->orderBy('id', $order)->paginate($this->productService->getLimit($request));
        }

        $productIds = $data->getCollection()->pluck('id');
        $collection = new ProductCollectionMini($data);
        $responseData = $collection->toArray($request);
        $responseData['filters'] = $filters;
        $responseData['categories'] = $this->getCollectionCategories($productIds);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $responseData);
    }

    private function getCollectionCategories($productIds): array
    {
        if ($productIds->isEmpty()) {
            return [];
        }

        return Category::query()
            ->active()
            ->whereNotNull('parent_id')
            ->whereHas('products', fn($q) => $q->whereIn('products.id', $productIds))
            ->get()
            ->map(fn($cat) => [
                'id'    => $cat->id,
                'name'  => $cat->getTranslation('name', app()->getLocale()),
                'slug'  => $cat->slug,
                'image' => [
                    'desktop' => $cat->getFirstMediaUrl('categories-desktop'),
                    'mobile'  => $cat->getFirstMediaUrl('categories-mobile'),
                ],
            ])
            ->values()
            ->toArray();
    }

    public function getProductBySlug(Request $request)
    {
        $slug = trim($request->route('slug'));
        $limit = $request->integer('limit', 10);
        $product =  $this->productService->getProductBySlug($slug, $limit);
        if (!$product) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductResource::make($product));
    }

    public function addProductReview(ReviewCreateRequest $request, $id)
    {

        $review =  $this->productService->addProductReview($request, $id);
        if (!$review) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
        return $this->apiResponse(REVIEW_CREATED_SUCCESSFULLY, 200, true);
    }
    public function updateProductReview(ReviewUpdateRequest $request, $id)
    {

        $review =  $this->productService->updateProductReview($request, $id);
        if (!$review) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
        return $this->apiResponse(REVIEW_UPDATED_SUCCESSFULLY, 200, true);
    }

    public function getBestProductSales(Request $request)
    {
        $products =  $this->productService->getBestProductSales($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getDiscountEndingTodayOrLowStockProducts(Request $request)
    {
        $products =  $this->productService->getDiscountEndingTodayOrLowStockProducts($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getNewArrivals(Request $request)
    {
        $products =  $this->productService->getNewArrivals($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getAllDiscountProducts(Request $request)
    {
        $products =  $this->productService->getAllDiscountProducts($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getProductForParentCategory(Request $request)
    {
        $products =  $this->productService->getProductForParentCategory($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }
}
