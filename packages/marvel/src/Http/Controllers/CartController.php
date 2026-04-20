<?php

namespace Marvel\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Marvel\Database\Repositories\CartRepository;
use Marvel\Http\Requests\CartCreateRequest;
use Marvel\Http\Requests\CartUpdateRequest;
use Marvel\Http\Resources\CartResource;
use Marvel\Traits\ApiResponse;

class CartController extends CoreController
{
    use ApiResponse;

    public $repository;

    public function __construct(CartRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $limit = $request->limit ?? 15;
        $query = $this->repository->with(['items.product']);
        $user = $request->user();

        if ($user) {
            $query->where('user_id', $user->id);
        } elseif ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $carts = $query->paginate($limit)->withQueryString();
        $data = CartResource::collection($carts)->response()->getData(true);

        return formatAPIResourcePaginate($data);
    }

    public function store(CartCreateRequest $request)
    {
        $cart = $this->repository->storeCart($request);
        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 201, true, CartResource::make($cart));
    }

    public function show(Request $request, $id)
    {
        $cart = $this->repository->with(['items.product'])->findOrFail($id);
        $user = $request->user();

        if ($user && (int) $cart->user_id !== (int) $user->id) {
            throw new AuthorizationException(NOT_AUTHORIZED);
        }

        return $this->apiResponse(FETCH_DATA_SUCCESSFULLY, 200, true, CartResource::make($cart));
    }

    public function update(CartUpdateRequest $request)
    {
        $cart = $this->repository->updateCart($request);
        return $this->apiResponse(UPDATE_CART_SUCCESSFULLY, 200, true, CartResource::make($cart));
    }

    public function deleteItemFromCart(Request $request, $ItemId)
    {
        $user = $request->user();
        $cart = $user->cart;
        if ($user && (int) $cart->user_id !== (int) $user->id) {
            throw new AuthorizationException(NOT_AUTHORIZED);
        }

        if(!$cart->items()->where('id', $ItemId)->delete()) {
            throw new AuthorizationException(INVALID_ITEM_DATA);
        }
        return $this->apiResponse(DELETE_CART_ITEM_SUCCESSFULLY, 200, true);
    }

    public function destroy(Request $request)
    {
        $cart = auth()->user()->cart;
        $user = $request->user();

        if ($user && (int) $cart->user_id !== (int) $user->id) {
            throw new AuthorizationException(NOT_AUTHORIZED);
        }

        $cart->items()->delete();
        return $this->apiResponse(DELETE_CART_SUCCESSFULLY, 200, true);
    }
}
