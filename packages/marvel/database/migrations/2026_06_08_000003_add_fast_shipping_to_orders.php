<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('shipping_method', ['SCHEDULED', 'FAST'])->default('SCHEDULED')->after('notes');
            $table->dateTime('expected_delivery_at')->nullable()->after('shipping_method');
            $table->decimal('fast_shipping_fee', 12, 2)->default(0)->after('expected_delivery_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method', 'expected_delivery_at', 'fast_shipping_fee']);
        });
    }
};
