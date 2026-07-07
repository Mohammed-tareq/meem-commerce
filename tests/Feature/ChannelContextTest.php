<?php

namespace Tests\Feature;

use App\Contexts\ChannelContext;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Marvel\Database\Models\Product;
use Tests\TestCase;

class ChannelContextTest extends TestCase
{
    use DatabaseTransactions;

    private const PREFIX = '/api/v1';

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('products')) {
            $this->createTables();
        }
    }

    private function createTables(): void
    {
        Schema::create('products', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('in_stock')->default(true);
            $table->integer('stock_quantity')->default(10);
            $table->integer('reserved_quantity')->default(0);
            $table->boolean('is_fast_shipping_available')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->string('event')->nullable();
            $table->timestamps();
            $table->index('log_name');
        });
    }

    private function seedProducts(): array
    {
        $normal = Product::create([
            'name' => 'Normal Product',
            'slug' => 'normal-product',
            'price' => 100,
            'status' => true,
            'in_stock' => true,
            'stock_quantity' => 10,
            'is_fast_shipping_available' => false,
        ]);

        $fast = Product::create([
            'name' => 'Fast Shipping Product',
            'slug' => 'fast-product',
            'price' => 150,
            'status' => true,
            'in_stock' => true,
            'stock_quantity' => 5,
            'is_fast_shipping_available' => true,
        ]);

        return [$normal, $fast];
    }

    /** @test */
    public function missing_channel_header_defaults_to_home()
    {
        $context = app(ChannelContext::class);

        $this->getJson(self::PREFIX . '/general/products?limit=1');

        $this->assertTrue($context->isHome());
    }

    /** @test */
    public function invalid_channel_header_in_non_strict_mode_falls_back_to_home()
    {
        config(['channel.strict' => false]);

        $context = app(ChannelContext::class);

        $this->getJson(self::PREFIX . '/general/products?limit=1', [
            'X-Channel' => 'invalid-channel',
        ]);

        $this->assertTrue($context->isHome());
    }

    /** @test */
    public function invalid_channel_header_in_strict_mode_returns_400()
    {
        config(['channel.strict' => true]);

        $response = $this->getJson(self::PREFIX . '/general/products?limit=1', [
            'X-Channel' => 'invalid-channel',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function empty_channel_header_defaults_to_home()
    {
        $context = app(ChannelContext::class);

        $this->getJson(self::PREFIX . '/general/products?limit=1', [
            'X-Channel' => '',
        ]);

        $this->assertTrue($context->isHome());
    }

    /** @test */
    public function fast_shipping_channel_filters_products()
    {
        [$normal, $fast] = $this->seedProducts();

        // Bypass Scout
        config(['scout.driver' => 'null']);

        $response = $this->getJson(self::PREFIX . '/general/products', [
            'X-Channel' => 'fast-shipping',
        ]);

        $response->assertOk();

        $productIds = collect($response->json('data.data'))->pluck('id')->toArray();
        $this->assertNotContains($normal->id, $productIds);
        $this->assertContains($fast->id, $productIds);
        $this->assertCount(1, $response->json('data.data'));
    }

    /** @test */
    public function home_channel_excludes_fast_shipping_products()
    {
        [$normal, $fast] = $this->seedProducts();

        config(['scout.driver' => 'null']);

        $response = $this->getJson(self::PREFIX . '/general/products');

        $response->assertOk();

        $productIds = collect($response->json('data.data'))->pluck('id')->toArray();
        $this->assertContains($normal->id, $productIds);
        $this->assertNotContains($fast->id, $productIds);
        $this->assertCount(1, $response->json('data.data'));
    }

    /** @test */
    public function channel_header_is_case_insensitive()
    {
        [$normal, $fast] = $this->seedProducts();

        config(['scout.driver' => 'null']);

        $response = $this->getJson(self::PREFIX . '/general/products', [
            'X-Channel' => 'FAST-SHIPPING',
        ]);

        $response->assertOk();
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('Fast Shipping Product', $response->json('data.data.0.name'));
    }

    /** @test */
    public function channel_disabled_config_stops_scope_from_filtering()
    {
        [$normal, $fast] = $this->seedProducts();

        config(['channel.enabled' => false]);
        config(['scout.driver' => 'null']);

        $response = $this->getJson(self::PREFIX . '/general/products', [
            'X-Channel' => 'fast-shipping',
        ]);

        $response->assertOk();

        $productIds = collect($response->json('data.data'))->pluck('id')->toArray();
        $this->assertContains($normal->id, $productIds);
        $this->assertContains($fast->id, $productIds);
        $this->assertCount(2, $response->json('data.data'));
    }

    /** @test */
    public function product_detail_respects_fast_shipping_scope()
    {
        [$normal, $fast] = $this->seedProducts();

        // Normal product should NOT be found in fast-shipping mode
        $response = $this->getJson(self::PREFIX . '/general/products/normal-product', [
            'X-Channel' => 'fast-shipping',
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function product_detail_shows_fast_shipping_product_in_fast_shipping_mode()
    {
        $this->seedProducts();

        $response = $this->getJson(self::PREFIX . '/general/products/fast-product', [
            'X-Channel' => 'fast-shipping',
        ]);

        $response->assertOk();
        $this->assertEquals('Fast Shipping Product', $response->json('data.name'));
    }
}
