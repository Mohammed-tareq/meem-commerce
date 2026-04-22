<?php

namespace Marvel\Http\Controllers;

use Illuminate\Http\Request;
use Marvel\Database\Repositories\BannerRepository;
use Marvel\Enums\Permission;
use Marvel\Http\Requests\BannerCreateRequest;
use Marvel\Http\Requests\BannerUpdateRequest;
use Marvel\Http\Resources\BannerResource;
use Marvel\Traits\ApiResponse;

class BannerController extends CoreController
{
    use ApiResponse;
    public $repository;
    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("permission:".Permission::VIEW_BANNERS)->only(["index","show"]);
        $this->middleware("permission:".Permission::CREATE_BANNERS)->only("store");
        $this->middleware("permission:".Permission::UPDATE_BANNERS)->only("update");
        $this->middleware("permission:".Permission::DELETE_BANNERS)->only("destroy");
    }

    public function index()
    {
        $banners = $this->repository->getBanners();
        $data = BannerResource::collection($banners)->response()->getData(true);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY,200, true, formatAPIResourcePaginate($data));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BannerCreateRequest $request)
    {
        try{
            $banner = $this->repository->createBanner($request);
            return $this->apiResponse(BANNER_CREATED_SUCCESSFULLY,200, true, BannerResource::make($banner));
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
            $banner = $this->repository->findOrFail($id);
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY,200, true, BannerResource::make($banner));
        }catch(\Exception $e){
            return $this->apiResponse(SOMETHING_WENT_WRONG,500, false);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BannerUpdateRequest $request, string $id)
    {
        try{
            $banner = $this->repository->updateBanner($request, $id);
            return $this->apiResponse(BANNER_UPDATED_SUCCESSFULLY,200, true, BannerResource::make($banner));
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
            $banner = $this->repository->findOrFail($id);
            $banner->delete();
            return $this->apiResponse(BANNER_DELETED_SUCCESSFULLY,200, true);
        }catch(\Exception $e){
            return $this->apiResponse(SOMETHING_WENT_WRONG,500, false, null);
        }
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banners,id',
        ]);
        $banner = $this->repository->changeStatus($request->id);
        if(!$banner){
            return $this->apiResponse(SOMETHING_WENT_WRONG,500, false);
        }
        return $this->apiResponse(BANNER_STATUS_CHANGED,200, true, BannerResource::make($banner));
    }


    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'banners' => 'required|array',
                'banners.*' => 'required|exists:banners,id',
            ]);
            $this->repository->reorder($request->banners);

            return $this->apiResponse(BANNERS_REORDERED_SUCCESSFULLY, 200, true);
        } catch (\Exception $e) {
            return $this->apiResponse(SOMETHING_WENT_WRONG, 500, false);
        }
    }
}
