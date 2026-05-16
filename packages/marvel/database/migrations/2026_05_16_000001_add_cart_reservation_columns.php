<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->after('quantity');
            $table->integer('reserved_quantity')->default(0)->after('stock_quantity');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->after('quantity');
            $table->integer('reserved_quantity')->default(0)->after('stock_quantity');
            $table->integer('sold_quantity')->default(0)->after('reserved_quantity');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->enum('status', ['active', 'expired', 'checked_out'])->default('active')->after('user_id');
            $table->timestamp('reserved_at')->nullable()->after('status');
            $table->timestamp('expires_at')->nullable()->after('reserved_at');
            $table->unique('user_id');
            $table->index(['user_id', 'status']);
            $table->index(['status', 'expires_at']);
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->integer('reserved_quantity')->default(0)->after('quantity');
            $table->index(['cart_id', 'product_id', 'product_variant_id']);
        });

        DB::table('products')->update([
            'stock_quantity' => DB::raw('quantity'),
        ]);

        DB::table('product_variants')->update([
            'stock_quantity' => DB::raw('quantity'),
        ]);
    }

    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['cart_id', 'product_id', 'product_variant_id']);
            $table->dropColumn('reserved_quantity');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['status', 'expires_at']);
            $table->dropColumn(['status', 'reserved_at', 'expires_at']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'reserved_quantity', 'sold_quantity']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'reserved_quantity']);
        });
    }
};
