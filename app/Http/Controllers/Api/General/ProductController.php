<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\ProductService;
use Illuminate\Http\Request;
use Marvel\Http\Resources\ProductCollection;
use Marvel\Http\Resources\ProductResource;
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
        $product =  $this->productService->getProductById($id);
        if (!$product) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true,ProductResource::make($product));
    }
}
