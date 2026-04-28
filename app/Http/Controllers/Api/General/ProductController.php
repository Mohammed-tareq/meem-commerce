<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\ProductService;
use Illuminate\Http\Request;
use Marvel\Http\Resources\ProductCollection;
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
}