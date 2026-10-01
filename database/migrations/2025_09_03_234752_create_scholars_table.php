<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('program')->nullable();
            $table->string('year_level')->nullable();
            $table->decimal('daily_allowance', 8, 2)->default(70.00);
            $table->decimal('current_balance', 8, 2)->default(0.00);
            $table->date('last_allowance_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('scholars');
    }
};
