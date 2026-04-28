<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use App\Services\General\SliderService;
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

    public function index()
    {
        $sliders =  $this->sliderService->getSliders();
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true,  SliderResource::collection($sliders));
    }
}
