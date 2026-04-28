<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSearchIndexes extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('price');
            $table->index('sold_quantity');
            $table->index('name');
            $table->index('slug');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('rating');
            $table->index(['rating', 'product_id']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->index('name');
            $table->index('slug');
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->index(['category_id', 'product_id']);
            $table->index(['product_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['price']);
            $table->dropIndex(['sold_quantity']);
            $table->dropIndex(['name']);
            $table->dropIndex(['slug']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['rating']);
            $table->dropIndex(['rating', 'product_id']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['name']);
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'product_id']);
            $table->dropIndex(['product_id', 'category_id']);
        });
    }
}