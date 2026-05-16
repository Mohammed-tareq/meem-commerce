<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'orders_user_id_created_at_index');
            $table->index('status', 'orders_status_index');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->index('order_id', 'order_products_order_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_id_created_at_index');
            $table->dropIndex('orders_status_index');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->dropIndex('order_products_order_id_index');
        });
    }
};
