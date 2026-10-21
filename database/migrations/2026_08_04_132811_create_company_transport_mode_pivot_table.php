<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_transport_mode', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('transport_mode_id')
                ->constrained('transport_modes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->primary(['company_id', 'transport_mode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_transport_mode');
    }
};
