
Schema::create('menus', function (Blueprint $table) {
    $table->boolean('is_archived')->default(false);
});

