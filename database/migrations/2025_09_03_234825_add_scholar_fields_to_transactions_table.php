<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('scholar_id')->nullable()->constrained('scholars')->onDelete('set null');
            $table->string('discount_type')->nullable();
            $table->boolean('is_scholar_transaction')->default(false);
        });
    }

    
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['scholar_id']);
            $table->dropColumn(['scholar_id', 'discount_type', 'is_scholar_transaction']);
        });
    }
};
