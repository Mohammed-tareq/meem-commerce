<?php

namespace Marvel\Database\Repositories;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\CartItem;
use Marvel\Database\Models\Product;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CartRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
    ];

    protected $dataArray = [
        'user_id',
    ];

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
            //
        }
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Cart::class;
    }

    public function storeCart(Request $request)
    {
        try {
            DB::beginTransaction();

            $userId = $request->user()?->id ?? $request->user_id;
            if (!$userId) {
                throw new AuthorizationException(NOT_AUTHORIZED);
            }

            $cart = $this->firstOrCreate([
                'user_id' => $userId,
            ]);

            if ($request->has('item')) {
                if (!$this->syncItems($cart, $request->item ?? [])) {
                    throw new Exception(INVALID_ITEM_DATA);
                }
            }
            DB::commit();
            return $cart->load(['items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function updateCart(Request $request)
    {
        try {
            DB::beginTransaction();

            $cart = $this->where('user_id', $request->user()->id)->firstOrFail();
            $userId = $request->user()->id;
            if ($userId && (int) $cart->user_id !== (int) $userId) {
                throw new AuthorizationException(NOT_AUTHORIZED);
            }

            if ($request->has('item')) {
                if (!$this->syncItems($cart, $request->item ?? [])) {
                    throw new Exception(INVALID_ITEM_DATA);
                }
            }

            DB::commit();
            return $cart->load(['items.product']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(500, $e->getMessage());
        }
    }

    private function syncItems(Cart $cart, array $item)
    {
        if (empty($item)) {
            return false;
        }

        $productId = $item['product_id'] ?? null;
        $quantity = (int) ($item['quantity'] ?? 0);

        if (!$productId || $quantity < 1) {
            return false;
            }

            $product = Product::findOrFail($productId);
        if($product->quantity < $quantity) {
            throw new Exception(QUANTITY_EXCEEDS_AVAILABLE_STOCK);
        }
        if (!$product->in_stock) {
            throw new Exception(PRODUCT_EXCEEDS_AVAILABLE_STOCK);
        }

            if ($product->has_flash_sale && $product->isFlashSaleValid()) {
                $price = $product->price_after_flash_sale ?? $product->getCurrentPrice();
                } else {
            $price = $product->getCurrentPrice();
        }
        $totalPrice = $price * $quantity;

        $cartItem = CartItem::where('cart_id', $cart->id)
        ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $totalPrice,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $totalPrice,
            ]);
        }

        return true;
    }


}
