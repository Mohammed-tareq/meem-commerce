<?php

namespace Marvel\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Marvel\Database\Repositories\CityRepository;
use Marvel\Http\Requests\CityStoreRequest;
use Marvel\Http\Requests\CityUpdateRequest;
use Marvel\Http\Resources\CityResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Cities",
 *     description="City management"
 * )
 */
class CityController extends CoreController
{
    public function __construct(private readonly CityRepository $repository)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/cities",
     *     tags={"Cities"},
     *     summary="List cities",
     *     @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="governorate_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $cities = $this->repository->paginate(
            (int) $request->get('per_page', 15),
            $request->get('search'),
            $request->get('governorate_id') ? (int) $request->get('governorate_id') : null
        );

        return response()->json([
            'status' => true,
            'message' => 'Cities fetched successfully.',
            'data' => CityResource::collection($cities),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cities",
     *     tags={"Cities"},
     *     summary="Create city",
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(CityStoreRequest $request): JsonResponse
    {
        try {
            $city = $this->repository->create($request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'City created successfully.',
            'data' => new CityResource($city),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/cities/{id}",
     *     tags={"Cities"},
     *     summary="Get city",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $city = $this->repository->findById($id, ['governorate']);

        if (!$city) {
            return response()->json(['status' => false, 'message' => 'City not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'City fetched successfully.',
            'data' => new CityResource($city),
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/cities/{id}",
     *     tags={"Cities"},
     *     summary="Update city",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function update(CityUpdateRequest $request, int $id): JsonResponse
    {
        $city = $this->repository->findById($id);

        if (!$city) {
            return response()->json(['status' => false, 'message' => 'City not found.'], 404);
        }

        try {
            $city = $this->repository->update($city, $request->validated());
        } catch (InvalidArgumentException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'City updated successfully.',
            'data' => new CityResource($city),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cities/{id}",
     *     tags={"Cities"},
     *     summary="Delete city",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $city = $this->repository->findById($id);

        if (!$city) {
            return response()->json(['status' => false, 'message' => 'City not found.'], 404);
        }

        $this->repository->delete($city);

        return response()->json([
            'status' => true,
            'message' => 'City deleted successfully.',
        ]);
    }
}
