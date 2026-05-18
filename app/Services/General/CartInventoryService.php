<?php

namespace App\Services\General;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\CartItem;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\ProductVariant;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\User;

class CartInventoryService
{
    private const CART_TTL_DAYS = 3;

    public function reserveItem(Cart $cart, Product $product, ?ProductVariant $variant, int $quantity, string $mode = 'add', array $attributes = []): CartItem
    {
        return DB::transaction(function () use ($cart, $product, $variant, $quantity, $mode, $attributes) {
            $cart = Cart::whereKey($cart->id)->lockForUpdate()->firstOrFail();
            $item = $this->findCartItemForLock($cart, $product->id, $variant?->id);
            $desiredQuantity = $mode === 'set'
                ? $quantity
                : (($item?->quantity ?? 0) + $quantity);

            if ($desiredQuantity < 1) {
                throw new Exception('Quantity must be at least 1.');
            }

            $stock = $this->lockInventoryRow($product, $variant);
            $reservedQuantity = (int) ($item?->reserved_quantity ?? 0);
            $delta = $desiredQuantity - $reservedQuantity;

            if ($delta > 0) {
                $this->reserveStock($stock, $delta);
            } elseif ($delta < 0) {
                $this->releaseStock($stock, abs($delta));
            }

            $price = $variant ? $variant->current_price : $product->current_price;
            $payload = [
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $desiredQuantity,
                'reserved_quantity' => $desiredQuantity,
                'price' => $price,
                'total_price' => $price * $desiredQuantity,
                'attributes' => $variant ? $attributes : null,
            ];

            if ($item) {
                $item->update($payload);
                $this->touchCartReservation($cart);

                return $item->refresh();
            }

            $item = $cart->items()->create($payload);
            $this->touchCartReservation($cart);

            return $item;
        });
    }

    public function reserveGiftItem(Cart $cart, Product $product, Promotion $promotion, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cart, $product, $promotion, $quantity) {
            $cart = Cart::whereKey($cart->id)->lockForUpdate()->firstOrFail();
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->whereNull('product_variant_id')
                ->where('promotion_id', $promotion->id)
                ->where('is_gift', true)
                ->lockForUpdate()
                ->first();

            $desiredQuantity = max(1, $quantity);
            $stock = $this->lockInventoryRow($product, null);
            $reservedQuantity = (int) ($item?->reserved_quantity ?? 0);
            $delta = $desiredQuantity - $reservedQuantity;

            if ($delta > 0) {
                $this->reserveStock($stock, $delta);
            } elseif ($delta < 0) {
                $this->releaseStock($stock, abs($delta));
            }

            $payload = [
                'product_id' => $product->id,
                'product_variant_id' => null,
                'quantity' => $desiredQuantity,
                'reserved_quantity' => $desiredQuantity,
                'price' => 0,
                'total_price' => 0,
                'attributes' => null,
                'is_gift' => true,
                'promotion_id' => $promotion->id,
            ];

            if ($item) {
                $item->update($payload);
                $this->touchCartReservation($cart);

                return $item->refresh();
            }

            $item = $cart->items()->create($payload);
            $this->touchCartReservation($cart);

            return $item;
        });
    }

    public function releaseItem(CartItem $item, bool $deleteItem = false): bool
    {
        return DB::transaction(function () use ($item, $deleteItem) {
            $item = CartItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            if ($item->reserved_quantity > 0) {
                $stock = $this->lockInventoryRowByItem($item);
                $this->releaseStock($stock, (int) $item->reserved_quantity);
            }

            if ($deleteItem) {
                return (bool) $item->delete();
            }

            return (bool) $item->update(['reserved_quantity' => 0]);
        });
    }

    public function releaseCart(Cart $cart, bool $deleteItems = false): bool
    {
        return DB::transaction(function () use ($cart, $deleteItems) {
            $cart = Cart::whereKey($cart->id)->lockForUpdate()->with('items')->firstOrFail();

            foreach ($cart->items as $item) {
                $this->releaseItem($item, $deleteItems);
            }

            $cart->update([
                'status' => 'active',
                'expires_at' => null,
                'reserved_at' => null,
                'total_price' => $deleteItems ? 0 : $cart->items()->sum('total_price'),
            ]);

            return true;
        });
    }

    public function finalizeCart(Cart $cart): bool
    {
        return DB::transaction(function () use ($cart) {
            $cart = Cart::whereKey($cart->id)->lockForUpdate()->with('items')->firstOrFail();

            foreach ($cart->items as $item) {
                if ($item->reserved_quantity > 0) {
                    $stock = $this->lockInventoryRowByItem($item);
                    $this->finalizeStock($stock, (int) $item->reserved_quantity);
                }

                $item->delete();
            }

            $cart->update([
                'status' => 'checked_out',
                'expires_at' => null,
                'reserved_at' => null,
                'total_price' => 0,
            ]);

            return true;
        });
    }

    public function expireCarts(): int
    {
        $expiredCount = 0;
        Cart::query()
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function ($carts) use (&$expiredCount) {
                foreach ($carts as $cart) {
                    $this->expireCart($cart);
                    $expiredCount++;
                }
            });

        return $expiredCount;
    }

    public function ensureCartReservation(Cart $cart): Cart
    {
        return DB::transaction(function () use ($cart) {
            $cart = Cart::whereKey($cart->id)
            ->lockForUpdate()
            ->with(['items.product', 'items.productVariant'])->firstOrFail();
            foreach ($cart->items as $item) {
                $this->syncCartItemReservation($item);
            }

            $this->touchCartReservation($cart);

            return $cart->refresh();
        });
    }

    public function getActiveCartForUser(User $user): ?Cart
    {
        return Cart::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['items.product', 'items.productVariant'])
            ->first();
    }

    private function syncCartItemReservation(CartItem $item): void
    {
        $item = CartItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
        $stock = $this->lockInventoryRowByItem($item);
        $desiredQuantity = (int) $item->quantity;
        $reservedQuantity = (int) $item->reserved_quantity;
        $delta = $desiredQuantity - $reservedQuantity;

        if ($delta > 0) {
            $this->reserveStock($stock, $delta);
        } elseif ($delta < 0) {
            $this->releaseStock($stock, abs($delta));
        }

        if ($delta !== 0) {
            $item->update(['reserved_quantity' => $desiredQuantity]);
        }
    }

    private function expireCart(Cart $cart): void
    {
        DB::transaction(function () use ($cart) {
            $cart = Cart::whereKey($cart->id)->lockForUpdate()->with('items')->firstOrFail();
            foreach ($cart->items as $item) {
                if ($item->reserved_quantity > 0) {
                    $stock = $this->lockInventoryRowByItem($item);
                    $this->releaseStock($stock, (int) $item->reserved_quantity);
                }
            }

            $cart->items()->delete();
            $cart->update([
                'status' => 'expired',
                'expires_at' => null,
                'reserved_at' => null,
                'total_price' => 0,
            ]);
        });
    }

    private function lockInventoryRow(Product $product, ?ProductVariant $variant)
    {
        if ($variant) {
            return ProductVariant::query()->whereKey($variant->id)->lockForUpdate()->firstOrFail();
        }

        return Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();
    }

    private function lockInventoryRowByItem(CartItem $item)
    {
        if ($item->product_variant_id) {
            return ProductVariant::query()->whereKey($item->product_variant_id)->lockForUpdate()->firstOrFail();
        }

        return Product::query()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();
    }

    private function reserveStock($stock, int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        $availableStock = $this->getAvailableStock($stock);
        if ($availableStock < $quantity) {
            throw new Exception('Quantity exceeds available stock.');
        }

        $stock->reserved_quantity = (int) ($stock->reserved_quantity ?? 0) + $quantity;
        $stock->in_stock = $availableStock - $quantity > 0;
        $stock->save();
    }

    private function releaseStock($stock, int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        $stock->reserved_quantity = max(0, (int) ($stock->reserved_quantity ?? 0) - $quantity);
        $stock->in_stock = $this->getAvailableStock($stock) > 0;
        $stock->save();
    }

    private function finalizeStock($stock, int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        $reservedQuantity = (int) ($stock->reserved_quantity ?? 0);
        $physicalQuantity = (int) ($stock->stock_quantity ?? 0);

        if ($reservedQuantity < $quantity) {
            throw new Exception('Reserved stock is insufficient.');
        }

        if ($physicalQuantity < $quantity) {
            throw new Exception('Physical stock is insufficient.');
        }

        $stock->stock_quantity = $physicalQuantity - $quantity;
        $stock->reserved_quantity = $reservedQuantity - $quantity;
        $stock->sold_quantity = (int) ($stock->sold_quantity ?? 0) + $quantity;
        $stock->in_stock = $this->getAvailableStock($stock) > 0;
        $stock->save();
    }

    private function findCartItemForLock(Cart $cart, int $productId, ?int $variantId): ?CartItem
    {
        $query = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->lockForUpdate();

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        } else {
            $query->whereNull('product_variant_id');
        }

        return $query->first();
    }

    private function touchCartReservation(Cart $cart): void
    {
        $cart->update([
            'status' => 'active',
            'reserved_at' => now(),
            'expires_at' => Carbon::now()->addDays(self::CART_TTL_DAYS),
        ]);
    }

    private function getAvailableStock($stock): int
    {
        return max(0, (int) ($stock->stock_quantity ?? 0) - (int) ($stock->reserved_quantity ?? 0));
    }
}