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
            return $cart->load(['items']);
        } catch (AuthorizationException $e) {
            DB::rollBack();
            throw new HttpException(401, $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(400, $e->getMessage());
        }
    }

    public function updateCart(Request $request)
    {
        try {
            DB::beginTransaction();

            $userId = $request->user()?->id ?? $request->user_id;
            if (!$userId) {
                throw new AuthorizationException(NOT_AUTHORIZED);
            }

            $cart = $this->where('user_id', $userId)->firstOrFail();

            if ($request->has('item')) {
                if (!$this->syncItems($cart, $request->item ?? [])) {
                    throw new Exception(INVALID_ITEM_DATA);
                }
            }

            DB::commit();
            return $cart->load(['items']);
        } catch (AuthorizationException $e) {
            DB::rollBack();
            throw new HttpException(401, $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(400, $e->getMessage());
        }
    }

    private function syncItems(Cart $cart, array $item)
    {
        if (empty($item)) {
            return false;
        }

        $productId = $item['product_id'] ?? null;
        $quantity = (int) ($item['quantity'] ?? 0);
        $variantId = $item['product_variant_id'] ?? null;

        if (!$productId || $quantity < 1) {
            return false;
        }
        $product = Product::findOrFail($productId);

        if (!$product->in_stock) {
            throw new Exception('Product exceeds available stock.');
        }

        if ($product->isSimple()) {
            if ($variantId) {
                throw new Exception(INVALID_ITEM_DATA);
            }

            $this->updateOrCreateCartForProductSimple($cart, $product, $quantity);
        } else {
            if (!$variantId) {
                throw new Exception(INVALID_ITEM_DATA);
            }

            $this->updateOrCreateCartForProductVariant($cart, $product, $variantId, $quantity);
        }
        return true;
    }






    protected function updateOrCreateCartForProductSimple($cart, $product, $quantity)
    {
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->whereNull('product_variant_id')
            ->first();

        if ($cartItem) {
            return $this->updateItemCartForProductSimple($cartItem, $product, $product->current_price, $quantity);
        }

        if ($product->quantity < $quantity) {
            throw new Exception('Quantity exceeds available stock.');
        }

        return $this->createItemCartForProductSimple($cart, $product, $product->current_price, $quantity);
    }

    protected function updateItemCartForProductSimple($cartItem, $product, $price, $quantity)
    {
        $newQuantity =  $quantity;

        if ($product->quantity < $newQuantity) {
            throw new Exception('Quantity exceeds available stock.');
        }

        return $cartItem->update([
            'quantity' => $newQuantity,
            'total_price' =>    $price * $newQuantity,
        ]);
    }

    protected function createItemCartForProductSimple($cart, $product, $price, $quantity)
    {

        return  $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'product_variant_id' => null,
            'price' => $price,
            'total_price' => $price * $quantity,
        ]);
    }

    protected function updateOrCreateCartForProductVariant($cart, $product, $variantId, $quantity)
    {
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variantId)
            ->where('product_id', $product->id)
            ->first();
        $variants = $product->variations()->whereId($variantId)->first();

        if (!$variants) {
            throw new Exception(INVALID_ITEM_DATA);
        }

        if ($cartItem) {
            return $this->updateItemCartForProductVariant($cartItem, $variants, $variants->current_price, $quantity);
        }

        if ($variants->quantity < $quantity) {
            throw new Exception('Quantity exceeds available stock.');
        }

        return $this->createItemCartForProductVariant($variants, $variants->current_price, $cart, $quantity);
    }

    protected function updateItemCartForProductVariant($cartItem, $variants, $variantPrice, $quantity)
    {
        $newQuantity = $cartItem->quantity + $quantity;

        if ($variants->quantity < $newQuantity) {
            throw new Exception('Quantity exceeds available stock.');
        }

        $totalPrice = $variantPrice * $quantity;
        return $cartItem->update([
            'quantity' => $newQuantity,
            'total_price' => $cartItem->total_price + $totalPrice,
        ]);
    }

    protected function createItemCartForProductVariant($variants, $variantPrice, $cart, $quantity)
    {
        $attributes = [];
        foreach ($variants->attributeProducts as $attribute) {
            $attributes[$attribute->attributeValue->attribute->name] = $attribute->attributeValue->value;
        }

        $totalPrice = $variantPrice * $quantity;
        return  $cart->items()->create([
            'product_id' => $variants->product->id,
            'quantity' => $quantity,
            'product_variant_id' => $variants->id,
            'price' => $variantPrice,
            'total_price' => $totalPrice,
            'attributes' => $attributes
        ]);
    }
}
