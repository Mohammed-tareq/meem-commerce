<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\HomeService;
use Illuminate\Http\Request;
use Marvel\Traits\ApiResponse;

class HomeController extends Controller
{
    use ApiResponse;

    private HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function index(Request $request)
    {
        $data = $this->homeService->getHomeData($request->integer('parent_category_id'));
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, $data);
    }
}
