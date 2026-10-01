<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained()->onDelete('set null');
            $table->string('image_path')->nullable();
            $table->integer('preparation_time')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('calories')->nullable();
            $table->json('ingredients')->nullable();
            $table->json('allergens')->nullable();
            $table->string('portion_size')->nullable();
            $table->tinyInteger('spicy_level')->default(0);
            $table->integer('order_count')->default(0);
            $table->integer('minimum_order_quantity')->default(1);
            $table->integer('maximum_order_quantity')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
