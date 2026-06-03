<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Marvel\Enums\FlashSaleType;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('slug');
            $table->text('description')->nullable();
            $table->date('start_date')->default(now());
            $table->date('end_date');
            $table->boolean('status')->default(true);
            $table->enum('type', FlashSaleType::getValues())->default(FlashSaleType::PERCENTAGE);
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('flash_sale_shop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('flash_sale_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('flash_sale_id')->references('id')->on('flash_sales')->onDelete('cascade');
            $table->softDeletes();
        });

        Schema::create('flash_sale_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('requested_product_ids')->nullable();
            $table->boolean('request_status')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->unsignedBigInteger('flash_sale_id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('flash_sale_id')->references('id')->on('flash_sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['flash_sale_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sales');
        Schema::dropIfExists('flash_sale_requests');
        Schema::dropIfExists('flash_sale_products');
    }
};