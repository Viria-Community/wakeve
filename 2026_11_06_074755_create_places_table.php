Schema::create('places', function (Blueprint $table) {
    $table->id();
    $table->string('name', 150);
    $table->string('place_type', 50)->nullable();
    $table->string('city', 100)->nullable();
    $table->string('province', 2)->nullable();
    $table->timestamps();
});
