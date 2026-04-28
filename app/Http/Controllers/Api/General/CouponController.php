<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\CouponService;
use Marvel\Traits\ApiResponse;
use Illuminate\Http\Request;
use Marvel\Http\Resources\CouponResource;

class CouponController extends Controller
{
    use ApiResponse;
    private $couponService;
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    public function index(Request $request)
    {
        $coupons = $this->couponService->getCoupons($request);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, CouponResource::collection($coupons));
    }
}