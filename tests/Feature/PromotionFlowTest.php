<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\General\PromotionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Marvel\Database\Models\Cart;
use Marvel\Database\Models\CartItem;
use Marvel\Database\Models\Product;
use Marvel\Database\Models\ProductVariant;
use Marvel\Database\Models\Promotion;
use Marvel\Database\Models\User;
use Marvel\Enums\ProductType;
use Marvel\Enums\PromotionMountType;
use Marvel\Enums\PromotionType;
use Tests\TestCase;

class PromotionFlowTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeSimpleProduct(string $name, float $price, int $stock): Product
    {
        return Product::create([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::uuid(),
            'price' => $price,
            'product_type' => ProductType::SIMPLE,
            'stock_quantity' => $stock,
            'reserved_quantity' => 0,
            'in_stock' => $stock > 0,
            'status' => true,
        ]);
    }

    /** @return array{product: Product, variant: ProductVariant} */
    private function makeVariableProductWithVariant(string $name, float $price, int $stock): array
    {
        $product = Product::create([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::uuid(),
            'price' => $price,
            'product_type' => ProductType::VARIABLE,
            'stock_quantity' => 0,
            'reserved_quantity' => 0,
            'in_stock' => true,
            'status' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'price' => $price,
            'sale_price' => null,
            'stock_quantity' => $stock,
            'reserved_quantity' => 0,
            'quantity' => $stock,
            'in_stock' => $stock > 0,
        ]);

        return ['product' => $product, 'variant' => $variant];
    }

    private function makeCartWithItem(User $user, Product $product): Cart
    {
        $cart = Cart::create([
            'user_id' => $user->id,
            'status' => 'active',
            'total_price' => 0,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'product_variant_id' => null,
            'quantity' => 1,
            'reserved_quantity' => 1,
            'price' => 100,
            'total_price' => 100,
            'attributes' => null,
        ]);

        return $cart;
    }

    public function test_checkout_promotions_returns_gift_variant_payload(): void
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user, [], 'api');

        $cartProduct = $this->makeSimpleProduct('Cart Item', 100, 10);
        $this->makeCartWithItem($user, $cartProduct);

        $giftData = $this->makeVariableProductWithVariant('Gift Product', 120, 5);
        $giftProduct = $giftData['product'];
        $giftVariant = $giftData['variant'];

        $giftPromotion = Promotion::create([
            'name' => 'Gift Promo',
            'code' => 'GIFT-' . Str::upper(Str::random(6)),
            'type' => PromotionType::QTY,
            'type_amount' => PromotionMountType::GIFT,
            'value' => 0,
            'discount' => 0,
            'minimum_order_amount' => 0,
            'required_quantity_type' => null,
            'apply_to' => 'all_products',
            'status' => true,
        ]);
        $giftPromotion->giftProducts()->attach($giftProduct->id, [
            'quantity' => 1,
            'product_variant_id' => $giftVariant->id,
        ]);

        Promotion::create([
            'name' => 'Fixed Promo',
            'code' => 'FIXED-' . Str::upper(Str::random(6)),
            'type' => PromotionType::PRICE,
            'type_amount' => PromotionMountType::FIXED_RATE,
            'value' => 10,
            'discount' => 10,
            'minimum_order_amount' => 0,
            'required_quantity_type' => null,
            'apply_to' => 'all_products',
            'status' => true,
        ]);

        Promotion::create([
            'name' => 'Percent Promo',
            'code' => 'PERC-' . Str::upper(Str::random(6)),
            'type' => PromotionType::PRICE,
            'type_amount' => PromotionMountType::PERCENTAGE,
            'value' => 10,
            'discount' => 10,
            'minimum_order_amount' => 0,
            'required_quantity_type' => null,
            'apply_to' => 'all_products',
            'status' => true,
        ]);

        $response = $this->getJson('/api/general/checkout/promotions');

        $response->assertOk();
        $promotions = $response->json('data.eligible_promotions');

        $this->assertNotEmpty($promotions);
        $types = collect($promotions)->pluck('type')->all();
        $this->assertContains('gift', $types);
        $this->assertContains('fixed_rate', $types);
        $this->assertContains('percentage', $types);

        $gift = collect($promotions)->firstWhere('type', 'gift');
        $this->assertNotNull($gift);
        $this->assertNotEmpty($gift['gift_items']);
        $this->assertEquals($giftVariant->id, $gift['gift_items'][0]['product_variant_id']);
        $this->assertEquals($giftVariant->id, $gift['gift_items'][0]['product_variant']['id']);
    }

    public function test_apply_selected_gift_promotion_reserves_variant(): void
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user, [], 'api');

        $cartProduct = $this->makeSimpleProduct('Cart Item', 100, 10);
        $cart = $this->makeCartWithItem($user, $cartProduct);

        $giftData = $this->makeVariableProductWithVariant('Gift Product', 120, 5);
        $giftProduct = $giftData['product'];
        $giftVariant = $giftData['variant'];

        $giftPromotion = Promotion::create([
            'name' => 'Gift Promo',
            'code' => 'GIFT-' . Str::upper(Str::random(6)),
            'type' => PromotionType::QTY,
            'type_amount' => PromotionMountType::GIFT,
            'value' => 0,
            'discount' => 0,
            'minimum_order_amount' => 0,
            'required_quantity_type' => null,
            'apply_to' => 'all_products',
            'status' => true,
        ]);
        $giftPromotion->giftProducts()->attach($giftProduct->id, [
            'quantity' => 1,
            'product_variant_id' => $giftVariant->id,
        ]);

        $service = app(PromotionService::class);
        $service->applySelectedPromotion($cart->fresh(), $giftPromotion->id, $giftProduct->id);

        $giftItem = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('is_gift', true)
            ->first();

        $this->assertNotNull($giftItem);
        $this->assertEquals($giftVariant->id, $giftItem->product_variant_id);
    }
}
