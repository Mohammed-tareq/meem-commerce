<?php

namespace Marvel\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Marvel\Database\Repositories\CartRepository;
use App\Services\General\CartInventoryService;
use Marvel\Http\Requests\CartCreateRequest;
use Marvel\Http\Requests\CartUpdateRequest;
use Marvel\Http\Resources\CartResource;
use Marvel\Traits\ApiResponse;

class CartController extends CoreController
{
    use ApiResponse;

    public $repository;
    public $inventoryService;

    public function __construct(CartRepository $repository, CartInventoryService $inventoryService)
    {
        $this->repository = $repository;
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 15;
        $query = $this->repository->with(['items.product', 'items.productVariant']);
        $user = $request->user();

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $carts = $query->paginate($limit)->withQueryString();
        $data = CartResource::collection($carts)->response()->getData(true);

        return formatAPIResourcePaginate($data);
    }

    public function store(CartCreateRequest $request)
    {
        try {
            $cart = $this->repository->storeCart($request);
            return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 201, true, CartResource::make($cart));
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 400, false);
        }
    }

    public function show(Request $request, $id)
    {
        $cart = $this->repository->with(['items.product', 'items.productVariant'])->findOrFail($id);
        $user = $request->user();

        if ($user && (int) $cart->user_id !== (int) $user->id) {
            throw new AuthorizationException(NOT_AUTHORIZED);
        }

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, CartResource::make($cart));
    }

    public function update(CartUpdateRequest $request)
    {
        try {
            $cart = $this->repository->updateCart($request);
            return $this->apiResponse(UPDATE_CART_SUCCESSFULLY, 200, true, CartResource::make($cart));
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 400, false);
        }
    }

    public function deleteItemFromCart(Request $request, $ItemId)
    {
        $user = $request->user();
        $cart = $user?->cart;
        if (!$cart) {
            return $this->apiResponse(DELETE_CART_ITEM_FAILED, 400, false);
        }
        if ($user && (int) $cart->user_id !== (int) $user->id) {
            return $this->apiResponse(DELETE_CART_ITEM_FAILED, 400, false);
        }

        $item = $cart->items()->where('id', $ItemId)->first();
        if (!$item) {
            return $this->apiResponse(DELETE_CART_ITEM_FAILED, 400, false);
        }

        if (!$this->inventoryService->releaseItem($item, true)) {
            return $this->apiResponse(DELETE_CART_ITEM_FAILED, 400, false);
        }

        $cart->update(['total_price' => $cart->items()->sum('total_price')]);
        return $this->apiResponse(DELETE_CART_ITEM_SUCCESSFULLY, 200, true);
    }

    public function destroy(Request $request)
    {
        $cart = auth()->user()?->cart;
        $user = $request->user();

        if (!$cart) {
            return $this->apiResponse(DELETE_CART_SUCCESSFULLY, 200, true);
        }

        if ($user && (int) $cart->user_id !== (int) $user->id) {
            return $this->apiResponse(DELETE_CART_ITEM_FAILED, 400, false);
        }

        $this->inventoryService->releaseCart($cart, true);
        return $this->apiResponse(DELETE_CART_SUCCESSFULLY, 200, true);
    }
}
