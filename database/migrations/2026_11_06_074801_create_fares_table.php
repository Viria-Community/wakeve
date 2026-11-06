<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fares', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2);
            $table->string('fare_class', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('line_id')->constrained('lines')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fares');
    }
};
