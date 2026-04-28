<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\CategoryService;
use Marvel\Http\Resources\CategoryCollection;
use Marvel\Traits\ApiResponse;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
    use ApiResponse;
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $categories = $this->categoryService->paginate($request);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY,200,true ,new CategoryCollection($categories));
    }
}