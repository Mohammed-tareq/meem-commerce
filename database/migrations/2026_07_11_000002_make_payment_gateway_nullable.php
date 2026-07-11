<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_gateway VARCHAR(50) NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE orders SET payment_gateway = 'myfatoorah' WHERE payment_gateway IS NULL");
        DB::statement("ALTER TABLE orders MODIFY payment_gateway VARCHAR(50) NOT NULL");
    }
};
