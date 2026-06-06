<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductMiniResource;
use App\Http\Resources\Product\ProductResource;
use App\Services\General\ProductService;
use Illuminate\Http\Request;
use Marvel\Http\Requests\ReviewCreateRequest;
use Marvel\Http\Requests\ReviewUpdateRequest;
use \Marvel\Http\Resources\product\ProductCollection;
use Marvel\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products =  $this->productService->paginate($request);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, new ProductCollection($products));
    }


    public function getProductById(Request $request)
    {
        $id = trim($request->route('id'));
        $limit = $request->integer('limit', 10);
        $product =  $this->productService->getProductById($id,$limit);
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
        $limit = $request->integer('limit', 10);
        $products =  $this->productService->getBestProductSales($limit);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getDiscountEndingTodayOrLowStockProducts(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $products =  $this->productService->getDiscountEndingTodayOrLowStockProducts($limit);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getNewArrivals(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $products =  $this->productService->getNewArrivals($limit);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getAllDiscountProducts(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $products =  $this->productService->getAllDiscountProducts($limit);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }

    public function getProductForParentCategory(Request $request)
    {
        $limit = $request->integer('limit', 10);
        $products =  $this->productService->getProductForParentCategory($limit);

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, ProductMiniResource::collection($products));
    }
}
