public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('transaction_number');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('total_amount', 10, 2);
        $table->decimal('amount_paid', 10, 2);
        $table->decimal('change_amount', 10, 2)->default(0);
        $table->string('payment_type')->default('cash');
        $table->string('charge_type')->nullable();  // Make sure these columns exist
        $table->string('charge_to')->nullable();    // and are properly defined
        $table->string('status')->default('completed');
        $table->foreignId('user_id')->nullable()->constrained();
        $table->timestamps();
    });
}
