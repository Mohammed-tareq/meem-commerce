<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderColumnToFlashSalesTable extends Migration
{
    public function up()
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
