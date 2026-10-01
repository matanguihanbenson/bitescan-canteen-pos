    public function up()
    {
        Schema::create('menu_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('menucategory_id');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->integer('display_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('menucategory_id')->references('id')->on('menu_categories');
        });
    }
