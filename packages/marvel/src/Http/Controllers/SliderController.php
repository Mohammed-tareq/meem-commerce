<?php

namespace Marvel\Http\Controllers;

use Marvel\Database\Repositories\SliderRepository;
use Marvel\Enums\Permission;
use Marvel\Http\Requests\SliderCreateRequest;
use Marvel\Http\Requests\SliderUpdateRequest;
use Marvel\Http\Resources\SliderResource;
use Marvel\Traits\ApiResponse;

class SliderController   extends CoreController
{
    use ApiResponse;
    public $repository;
    public function __construct(SliderRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("permission:".Permission::VIEW_SLIDER)->only(["index","show"]);
        $this->middleware("permission:".Permission::CREATE_SLIDER)->only("store");
        $this->middleware("permission:".Permission::UPDATE_SLIDER)->only("update");
        $this->middleware("permission:".Permission::DELETE_SLIDER)->only("destroy");
    }

    public function index()
    {
        $sliders = $this->repository->getSliders();
        $data = SliderResource::collection($sliders)->response()->getData(true);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY,200, true, formatAPIResourcePaginate($data));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SliderCreateRequest $request)
    {
        try{
            $slider = $this->repository->createSlider($request);
            return $this->apiResponse(SLIDER_CREATED_SUCCESSFULLY,200, true, SliderResource::make($slider));
        }catch(\Exception $e){
            return $this->apiResponse(SOMETHING_WENT_WRONG,500, false);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $slider = $this->repository->findOrFail($id);
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY,200, true, SliderResource::make($slider));
        }catch(\Exception $e){
            return $this->apiResponse(SOMETHING_WENT_WRONG,500, false);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SliderUpdateRequest $request, string $id)
    {
        try{
            $slider = $this->repository->updateSlider($request, $id);
            return $this->apiResponse(SLIDER_UPDATED_SUCCESSFULLY,200, true, SliderResource::make($slider));
        }catch(\Exception $e){
            return $this->apiResponse(SOMETHING_WENT_WRONG,500, false);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $slider = $this->repository->findOrFail($id);
            $slider->delete();
            return $this->apiResponse(SLIDER_DELETED_SUCCESSFULLY,200, true);
        }catch(\Exception $e){
            return $this->apiResponse(SOMETHING_WENT_WRONG,500, false, null);
        }
    }
}