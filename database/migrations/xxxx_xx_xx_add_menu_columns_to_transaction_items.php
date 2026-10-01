<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_id')->nullable()->after('product_name');
            $table->string('menu_code')->nullable()->after('menu_id');
            $table->string('menu_name')->nullable()->after('menu_code');
            $table->string('product_code')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropColumn(['menu_id', 'menu_code', 'menu_name']);
            $table->integer('product_code')->change();
        });
    }
};
