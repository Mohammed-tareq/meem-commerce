<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\SliderService;
use Illuminate\Http\Request;
use Marvel\Http\Resources\SliderResource;
use Marvel\Traits\ApiResponse;

class SliderController extends Controller
{
    use ApiResponse;
    private SliderService $sliderService;

    public function __construct(SliderService $sliderService)
    {
        $this->sliderService = $sliderService;
    }

    public function index( Request $request)
    {
        $sliders =  $this->sliderService->getSliders($request);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true,  SliderResource::collection($sliders));
    }
}