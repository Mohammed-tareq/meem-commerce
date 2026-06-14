<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductMiniResource;
use App\Http\Resources\Product\ProductResource;
use App\Services\General\ProductEngine\ProductStrategyResolver;
use App\Services\General\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function index(Request $request)
    {
        $type = $request->query('type', '');
        if (!empty($type)) {
            $handler = $this->productStrategyResolver->resolve($type);
            $data = Cache::remember('products_' . $type, 60, function () use ($handler, $request) {
                return $handler->getProducts($request);
            });
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($data));
        }

        $cacheKey = 'products_index_' . md5(json_encode($request->all()) . '_' . app()->getLocale());

        $responseData = Cache::remember($cacheKey, 60, function () use ($request) {
            $query = $this->productService->buildFilteredBaseQuery($request);
            $filters = $this->productService->getDynamicFilters(clone $query);
            $orderPrice = $request->query('order_price');
            if (in_array($orderPrice, ['asc', 'desc'])) {
                $query->orderBy('price', $orderPrice);
            }
            $data = $query->orderByDesc('id')->paginate($this->productService->getLimit($request));
            $collection = new ProductCollectionMini($data);
            $collectionArray = $collection->toArray($request);
            $collectionArray['filters'] = $filters;
            return $collectionArray;
        });

        $responseData['category'] = $this->resolveCategoryForResponse($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $responseData);
    }

    private function resolveCategoryForResponse(Request $request): ?array
    {
        $categoryFilter = $request->query('category');
        if (empty($categoryFilter)) {
            return null;
        }

        $categoryNames = is_array($categoryFilter) ? $categoryFilter : explode(',', $categoryFilter);
        $value = trim($categoryNames[0]);
        if (empty($value)) {
            return [];
        }

        $category = Category::query()
            ->active()
            ->with(['children' => function ($query) {
                $query->active();
            }])
            ->where(function ($q) use ($value) {
                $q->where('slug', $value)
                  ->orWhere('name->' . app()->getLocale(), $value);
            })
            ->first();

        if (!$category) {
            return [];
        }

        return $category->children->map(fn($child) => [
            'id'    => $child->id,
            'name'  => $child->getTranslation('name', app()->getLocale()),
            'slug'  => $child->slug,
            'image' => [
                'desktop' => $child->getFirstMediaUrl('categories-desktop'),
                'mobile'  => $child->getFirstMediaUrl('categories-mobile'),
            ],
        ])->values()->toArray();
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
