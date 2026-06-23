<?php

namespace Marvel\Database\Repositories;

use App\Services\General\CartInventoryService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\Product;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CartRepository extends BaseRepository
{
    protected $fieldSearchable = [
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

    public function model()
    {
        return Cart::class;
    }

    public function storeCart(Request $request)
    {

        return $this->persistCart($request, 'add');
    }

    public function updateCart(Request $request)
    {
        return $this->persistCart($request, 'set');
    }

    private function persistCart(Request $request, string $mode)
    {
        try {
            DB::beginTransaction();

            $userId = $request->user()?->id;
            if (!$userId) {
                throw new AuthorizationException(NOT_AUTHORIZED);
            }
            
            $cart = Cart::query()
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();
            
            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $userId,
                    'status' => 'active',
                ]);
            }
            
            $cart->update(['status' => 'active']);

            if ($request->filled('item')) {
                if (!$this->syncItems($cart, $request->item ?? [], $mode)) {
                    throw new Exception(INVALID_ITEM_DATA);
                }
            }

            $cart->update([
                'total_price' => $cart->items()->sum('total_price'),
            ]);

            DB::commit();

            return $cart->load(['items.product', 'items.productVariant.attributeProducts.attributeValue.attribute']);
        } catch (AuthorizationException $e) {
            DB::rollBack();
            throw new HttpException(401, $e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpException(400, $e->getMessage());
        }
    }

    private function syncItems(Cart $cart, array $item, string $mode): bool
    {
        $productId = $item['product_id'] ?? null;
        $quantity = (int) ($item['quantity'] ?? 0);
        $variantId = $item['product_variant_id'] ?? null;
        $attributes = $item['attributes'] ?? [];
        $shippingMethod = $item['shipping_method'] ?? 'SCHEDULED';

        if (!$productId || $quantity < 1) {
            return false;
        }

        $product = Product::findOrFail($productId);

        if ($shippingMethod === 'FAST' && !$product->is_fast_shipping_available) {
            throw new Exception(FAST_SHIPPING_PRODUCT_NOT_ELIGIBLE);
        }

        $inventoryService = app(CartInventoryService::class);

        if ($variantId) {
            $variant = $product->variations()->whereKey($variantId)->first();
            if (!$variant) {
                throw new Exception(INVALID_ITEM_DATA);
            }

            if ($variant->available_stock < $quantity) {
                throw new Exception('Quantity exceeds available stock.');
            }

            $inventoryService->reserveItem($cart, $product, $variant, $quantity, $mode, $attributes, $shippingMethod);
            return true;
        }

        if ($product->product_type === 'variable') {
            throw new Exception(INVALID_ITEM_DATA);
        }

        if ($product->available_stock < $quantity) {
            throw new Exception('Product exceeds available stock.');
        }

        $inventoryService->reserveItem($cart, $product, null, $quantity, $mode, $attributes, $shippingMethod);
        return true;
    }
}
