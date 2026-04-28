<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\BannerService;
use Marvel\Http\Resources\BannerResource;
use Marvel\Traits\ApiResponse;

class BannerController extends Controller
{
    use ApiResponse;
    private BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function index()
    {
        $banners =  $this->bannerService->getBanners();
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true,  BannerResource::collection($banners));
    }
}
