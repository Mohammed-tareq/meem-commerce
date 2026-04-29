<?php

namespace Marvel\Http\Controllers;

use Illuminate\Http\Request;
use Marvel\Exceptions\MarvelException;
use Marvel\Database\Repositories\PromotionRepository;
use Marvel\Enums\Permission;
use Marvel\Http\Requests\PromotionRequest;
use Marvel\Http\Requests\UpdatePromotionRequest;
use Marvel\Http\Resources\PromotionResource;
use Marvel\Traits\ApiResponse;
use Throwable;

class PromotionController extends CoreController
{
    use ApiResponse;

    public $repository;

    public function __construct(PromotionRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("permission:" . Permission::VIEW_PROMOTION, ["only" => ["index", "show"]]);
        $this->middleware("permission:" . Permission::CREATE_PROMOTION, ["only" => ["store"]]);
        $this->middleware("permission:" . Permission::UPDATE_PROMOTION, ["only" => ["update"]]);
        $this->middleware("permission:" . Permission::DELETE_PROMOTION, ["only" => ["destroy"]]);
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 15;
        $promotions = $this->repository->paginate($limit)->withQueryString();
        return formatAPIResourcePaginate(PromotionResource::collection($promotions)->response()->getData(true));
    }

    public function store(PromotionRequest $request)
    {
        try {
            $promotion = $this->repository->storePromotion($request);
            return $this->apiResponse(CREATED_PROMOTION_SUCCESSFULLY, 201, true, PromotionResource::make($promotion));
        } catch (MarvelException $e) {
            return $this->apiResponse(COULD_NOT_CREATE_THE_RESOURCE, 400, false);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $promotion = $this->repository->findOrFail($id);
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, PromotionResource::make($promotion));
        } catch (Throwable $e) {
            return $this->apiResponse(NOT_FOUND, 404, false);
        }
    }

    public function update(UpdatePromotionRequest $request, $id)
    {
        try {
            $promotion = $this->repository->updatePromotion($id, $request);
            return $this->apiResponse(UPDATED_PROMOTION_SUCCESSFULLY, 200, true, PromotionResource::make($promotion));
        } catch (MarvelException $e) {
            return $this->apiResponse(COULD_NOT_UPDATE_THE_RESOURCE, 400, false);
        }
    }

    public function destroy($id)
    {
        try {
            $promotion = $this->repository->findOrFail($id);
            $promotion->delete();
            return $this->apiResponse(DELETED_PROMOTION_SUCCESSFULLY, 200, true);
        } catch (Throwable $e) {
            return $this->apiResponse(COULD_NOT_DELETE_THE_RESOURCE, 400, false);
        }
    }
}
