<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Http\Resources\Promotion\PromotionResource;
use App\Services\General\PromotionDataService;
use Illuminate\Http\Request;
use Marvel\Traits\ApiResponse;

class  PromotionController extends Controller
{
     use ApiResponse;
    private PromotionDataService $promotionService;

    public function __construct(PromotionDataService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index( Request $request)
    {
        $promotions =  $this->promotionService->paginatePromotion($request);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true,  PromotionResource::collection($promotions));
    }
    public function getPromotionBySlug($slug)
    {
        $PromotionWithProducts  = $this->promotionService->getPromotionBySlug($slug);
        if (!$PromotionWithProducts) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
        
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, PromotionResource::make($PromotionWithProducts));
    }
}