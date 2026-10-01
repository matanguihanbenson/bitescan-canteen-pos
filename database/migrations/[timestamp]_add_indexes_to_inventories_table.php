<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->index('current_stock', 'idx_current_stock');
            $table->index('exp_date', 'idx_exp_date');
            $table->index(['product_code', 'current_stock'], 'idx_product_stock');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->index('product_name', 'idx_product_name');
        });
    }

    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('idx_current_stock');
            $table->dropIndex('idx_exp_date');
            $table->dropIndex('idx_product_stock');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_product_name');
        });
    }
};
