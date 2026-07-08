<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // ── Orders: handle existing nulls before adding constraints ──

        DB::statement("UPDATE orders SET fulfillment_type = 'delivery' WHERE fulfillment_type IS NULL");
        DB::statement("UPDATE orders SET payment_method = 'online' WHERE payment_method IS NULL");
        DB::statement("UPDATE orders SET payment_gateway = 'myfatoorah' WHERE payment_gateway IS NULL");
        DB::statement("UPDATE orders SET shipping_price = 0 WHERE shipping_price IS NULL");

        DB::statement("ALTER TABLE orders MODIFY fulfillment_type VARCHAR(20) NOT NULL DEFAULT 'delivery'");
        DB::statement("ALTER TABLE orders MODIFY payment_method VARCHAR(30) NOT NULL");
        DB::statement("ALTER TABLE orders MODIFY payment_gateway VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE orders MODIFY shipping_price DECIMAL(8,3) NOT NULL DEFAULT 0");

        // ── Transactions: handle existing nulls ──

        $nullUuids = DB::table('transactions')->whereNull('uuid')->get();
        foreach ($nullUuids as $row) {
            DB::table('transactions')
                ->where('id', $row->id)
                ->update(['uuid' => (string) Str::uuid()]);
        }

        DB::statement("UPDATE transactions t
            SET t.amount = (SELECT o.total_price FROM orders o WHERE o.id = t.order_id)
            WHERE t.amount IS NULL");

        DB::statement("ALTER TABLE transactions MODIFY uuid CHAR(36) NOT NULL");
        DB::statement("ALTER TABLE transactions MODIFY amount DECIMAL(10,2) NOT NULL DEFAULT 0");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY fulfillment_type VARCHAR(20) NULL");
        DB::statement("ALTER TABLE orders MODIFY payment_method VARCHAR(30) NULL");
        DB::statement("ALTER TABLE orders MODIFY payment_gateway VARCHAR(50) NULL");
        DB::statement("ALTER TABLE orders MODIFY shipping_price DECIMAL(8,3) NULL");

        DB::statement("ALTER TABLE transactions MODIFY uuid CHAR(36) NULL");
        DB::statement("ALTER TABLE transactions MODIFY amount DECIMAL(10,2) NULL");
    }
};
