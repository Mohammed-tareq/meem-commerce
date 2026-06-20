<?php

namespace Marvel\Http\Controllers;

use Illuminate\Http\Request;
use Marvel\Database\Repositories\BrandRepository;
use Marvel\Enums\Permission;
use Marvel\Exceptions\MarvelException;
use Marvel\Http\Requests\BrandCreateRequest;
use Marvel\Http\Requests\BrandUpdateRequest;
use Marvel\Http\Resources\BrandResource;
use Marvel\Traits\ApiResponse;

class BrandController extends CoreController
{
    use ApiResponse;

    public $repository;

    public function __construct(BrandRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware('permission:' . Permission::VIEW_BRANDS, ['only' => ['index']]);
        $this->middleware('permission:' . Permission::VIEW_BRANDS, ['only' => ['show']]);
        $this->middleware('permission:' . Permission::CREATE_BRAND, ['only' => ['store']]);
        $this->middleware('permission:' . Permission::UPDATE_BRAND, ['only' => ['update']]);
        $this->middleware('permission:' . Permission::DELETE_BRAND, ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 15;
        $active = $request->active ?? null;
        $inactive = $request->inactive ?? null;
        $search = $request->search ?? null;

        $brandsQuery = $this->repository;

        if ($active) {
            $brandsQuery = $brandsQuery->active();
        }
        if ($inactive) {
            $brandsQuery = $brandsQuery->inactive();
        }
        if ($search) {
            $brandsQuery = $brandsQuery->search('name', $search, app()->getLocale());
        }

        $brands = $brandsQuery->paginate($limit);
        $data = BrandResource::collection($brands)->response()->getData(true);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, formatAPIResourcePaginate($data));
    }

    public function store(BrandCreateRequest $request)
    {
        try {
            $brand = $this->repository->saveBrand($request);
            return $this->apiResponse(BRAND_CREATED_SUCCESSFULLY, 200, true, BrandResource::make($brand));
        } catch (MarvelException $th) {
            throw new MarvelException(COULD_NOT_CREATE_THE_RESOURCE);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $brand = $this->repository->where('id', $id)->firstOrFail();
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, BrandResource::make($brand));
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }

    public function update(BrandUpdateRequest $request, $id)
    {
        try {
            $request->merge(['id' => $id]);
            $brand = $this->brandUpdate($request);
            return $this->apiResponse(BRAND_UPDATED_SUCCESSFULLY, 200, true, BrandResource::make($brand));
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }

    public function brandUpdate(BrandUpdateRequest $request)
    {
        $brand = $this->repository->findOrFail($request->id);
        return $this->repository->updateBrand($request, $brand);
    }

    public function destroy($id)
    {
        try {
            $this->repository->findOrFail($id)->delete();
            return $this->apiResponse(BRAND_DELETED_SUCCESSFULLY, 200, true);
        } catch (MarvelException $e) {
            throw new MarvelException(NOT_FOUND);
        }
    }
}
