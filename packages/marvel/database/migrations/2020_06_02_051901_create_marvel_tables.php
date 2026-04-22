<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Marvel\Enums\CouponType;
use Marvel\Enums\DiscountType;
use Marvel\Enums\OrderStatus;
use Marvel\Enums\PaymentStatus;
use Marvel\Enums\ProductStatus;
use Marvel\Enums\ProductType;
use Marvel\Enums\ShippingType;

class CreateMarvelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('amount');
            $table->string('is_global')->default(true);
            $table->enum('type', ShippingType::getValues())->default(ShippingType::FIXED);
            $table->timestamps();
        });
        Schema::create('coupons', function (Blueprint $table) {
            //     $table->text('description')->nullable();
            //     $table->json('image')->nullable();
            //     $table->enum('type', CouponType::getValues())->default(CouponType::FIXED_COUPON);
            //     $table->float('amount')->default(0);
            //     $table->string('code');
            //     $table->float('minimum_cart_amount')->default(0);
            //     $table->string('active_from');
            //     $table->string('expire_at');
            //     $table->timestamp('deleted_at')->nullable();
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable();
            $table->decimal('discount', 8, 3)->nullable();
            $table->enum('discount_type', DiscountType::getValues())->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('limiter')->nullable();
            $table->integer('used')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });


        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->json('promotional_sliders')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_approved')->default(false);
            $table->json('image')->nullable();
            $table->json('cover_image')->nullable();
            $table->string('slug');
            $table->text('bio')->nullable();
            $table->text('quote')->nullable();
            $table->string('born')->nullable();
            $table->string('death')->nullable();
            $table->string('languages')->nullable();
            $table->json('socials')->nullable();
            $table->timestamps();
        });

        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_approved')->default(false);
            $table->json('image')->nullable();
            $table->json('cover_image')->nullable();
            $table->string('slug');
            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->json('socials')->nullable();
            $table->timestamps();
        });
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('order');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            // $table->json('image')->nullable();
            // $table->unsignedBigInteger('type_id');
            // $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
        });
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['slider', 'image'])->default('slider');
            $table->integer('order');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            // $table->json('image')->nullable();
            // $table->unsignedBigInteger('type_id');
            // $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->double('price')->nullable();
            $table->string('sku')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('sold_quantity')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->enum('status', ProductStatus::getValues())->default(ProductStatus::DRAFT);
            // $table->enum('product_type', ProductType::getValues())->default(ProductType::SIMPLE);
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('length')->nullable();
            $table->string('weight')->nullable();
            $table->boolean('has_flash_sale')->default(false);
            $table->boolean('has_discount')->default(false);
            $table->unsignedBigInteger('banner_id')->nullable();
            $table->foreign('banner_id')->references('id')->on('banners')->nullOnDelete();
            $table->enum('discount_type', DiscountType::getValues())->default(DiscountType::PERCENTAGE);
            $table->double('amount')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->double('price_after_discount')->nullable();
            $table->double('price_after_flash_sale')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        // Schema::create('discounts', function (Blueprint $table) {
        //     $table->id();
        //     $table->enum('discount_type', DiscountType::getValues())->default(DiscountType::PERCENTAGE);
        //     $table->double('amount')->default(0);
        //     $table->date('start_date')->nullable();
        //     $table->date('end_date')->nullable();
        //     $table->double('price_after_discount')->nullable();
        //     $table->unsignedBigInteger('product_id');
        //     $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        //     $table->timestamps();
        // });
        Schema::create('orders', function (Blueprint $table) {
            // $table->id();
            // $table->string('tracking_number')->unique();
            // $table->unsignedBigInteger('customer_id')->nullable();
            // $table->string('customer_contact');
            // $table->string('customer_name')->nullable();
            // $table->double('amount');
            // $table->double('sales_tax')->nullable();
            // $table->double('paid_total')->nullable();
            // $table->double('total')->nullable();
            // $table->unsignedBigInteger('coupon_id')->nullable();
            // $table->double('discount')->nullable();
            // $table->string('payment_gateway')->nullable();
            // $table->string('altered_payment_gateway')->nullable();
            // $table->json('shipping_address')->nullable();
            // $table->json('billing_address')->nullable();
            // $table->unsignedBigInteger('logistics_provider')->nullable();
            // $table->double('delivery_fee')->nullable();
            // $table->string('delivery_time')->nullable();
            // $table->enum('order_status', OrderStatus::getValues())->default(OrderStatus::PENDING);
            // $table->enum('payment_status', PaymentStatus::getValues())->default(PaymentStatus::PENDING);
            // $table->softDeletes();
            // $table->timestamps();
            // $table->foreign('customer_id')->references('id')->on('users');

            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('user_phone');
            $table->string('user_email');
            $table->string('address');


            $table->string('notes')->nullable();

            $table->decimal('price', 8, 3);
            $table->decimal('shipping_price', 8, 3);
            $table->decimal('total_price', 8, 3);

            $table->string('coupon')->nullable();
            $table->decimal('coupon_discount', 10, 3)->nullable();
            $table->string('coupon_discount_type')->nullable();

            $table->enum('status', ['pending', 'completed', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        Schema::create('order_products', function (Blueprint $table) {
            // $table->id();
            // $table->unsignedBigInteger('order_id');
            // $table->unsignedBigInteger('product_id');
            // $table->string('order_quantity');
            // $table->double('unit_price');
            // $table->double('subtotal');
            // $table->softDeletes();
            // $table->timestamps();
            // $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->string('product_name');
            $table->string('product_desc');
            $table->integer('product_quantity');
            $table->decimal('product_price', 8, 3);
            $table->decimal('product_discount', 10, 3)->nullable();
            $table->timestamps();
        });
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_id');
            $table->bigInteger('user_id');
            $table->string('payment_method');
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug');
            $table->text('details')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            // $table->string('icon')->nullable();
            // $table->json('image')->nullable();
            // $table->json('banner_image')->nullable();
            // $table->unsignedBigInteger('type_id');
            //            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('coupon')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->unsignedBigInteger('attribute_id');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('product_varaints', function (Blueprint $table) {
            $table->id();
            $table->double('price')->nullable();
            $table->double('sale_price')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('length')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('attribute_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_value_id');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
            $table->unsignedBigInteger('product_variant_id');
            $table->foreign('product_variant_id')->references('id')->on('product_varaints')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('tax_classes', function (Blueprint $table) {
            $table->id();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->double('rate');
            $table->string('name')->nullable();
            $table->integer('is_global')->nullable();
            $table->integer('priority')->nullable();
            $table->boolean('on_shipping')->default(1);
            $table->timestamps();
        });

        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->boolean('default')->default(false);
            $table->json('address');
            $table->json('location')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->text('site_desc')->nullable();
            $table->text('meta_desc')->nullable();
            $table->string('site_copy_right')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('site_email')->nullable();
            $table->string('email_support')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('promotion_video_url')->nullable();
            $table->string('youtube')->nullable();
            $table->string('phone')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->json('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->json('socials')->nullable();
            $table->string('contact')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(1);
        });

        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('url')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_classes');
        Schema::dropIfExists('shipping_classes');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('types');
        Schema::dropIfExists('products');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attribute_product');
        Schema::dropIfExists('tax_classes');
        Schema::dropIfExists('address');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('banners');
    }
}
