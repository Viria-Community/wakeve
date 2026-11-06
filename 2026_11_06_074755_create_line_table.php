Schema::create('lines', function (Blueprint $table) {
    $table->id();
    $table->string('label', 150);
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
    $table->foreignId('transport_mode_id')->constrained('transport_modes')->restrictOnDelete();
    $table->foreignId('origin_place_id')->constrained('places')->restrictOnDelete();
    $table->foreignId('destination_place_id')->constrained('places')->restrictOnDelete();
    $table->timestamps();
    $table->index('status');
});
