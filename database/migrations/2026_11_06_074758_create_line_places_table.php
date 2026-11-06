<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_places', function (Blueprint $table) {
            $table->foreignId('line_id')->constrained('lines')->restrictOnDelete();
            $table->foreignId('place_id')->constrained('places')->restrictOnDelete();
            $table->unsignedSmallInteger('stop_order');
            $table->primary(['line_id', 'place_id']);
            $table->unique(['line_id', 'stop_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_places');
    }
};
