<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100)->unique();
            $table->enum('applicable_to', ['company', 'fleet_unit', 'both']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
