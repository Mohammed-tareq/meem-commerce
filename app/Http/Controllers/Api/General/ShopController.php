<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\ShopService;
use Illuminate\Http\Request;
use Marvel\Http\Resources\ShopCollection;
use Marvel\Http\Resources\ShopResource;
use Marvel\Traits\ApiResponse;

class ShopController extends Controller
{
    use ApiResponse;
    private ShopService $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function index(Request $request)
    {
        $shops =  $this->shopService->paginate($request);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, new ShopCollection($shops));
    }

    public function getShopById(Request $request)
    {
        $id = trim($request->route('id'));
        $shop =  $this->shopService->getShopById($id);
        if (!$shop) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true,  ShopResource::make($shop));
    }
}
