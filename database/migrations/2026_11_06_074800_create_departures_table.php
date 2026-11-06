<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departures', function (Blueprint $table) {
            $table->id();
            $table->date('departure_date');
            $table->time('departure_time');
            $table->enum('status', ['scheduled', 'cancelled'])->default('scheduled');
            $table->foreignId('line_id')->constrained('lines')->restrictOnDelete();
            $table->foreignId('fleet_unit_id')->constrained('fleet_units')->restrictOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['line_id', 'departure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departures');
    }
};
