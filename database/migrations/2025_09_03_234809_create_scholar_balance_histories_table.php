<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('scholar_balance_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholar_id')->constrained('scholars')->onDelete('cascade');
            $table->enum('transaction_type', ['debit', 'credit']);
            $table->decimal('amount', 8, 2);
            $table->decimal('previous_balance', 8, 2);
            $table->decimal('new_balance', 8, 2);
            $table->string('description');
            $table->string('transaction_number')->nullable();
            $table->date('transaction_date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['scholar_id', 'transaction_date']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('scholar_balance_histories');
    }
};
