<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('previous_status', ['pending', 'validated', 'rejected', 'active', 'suspended'])
                ->nullable();
            $table->enum('new_status', ['pending', 'validated', 'rejected', 'active', 'suspended']);
            $table->string('reason', 255)->nullable();
            $table->foreignId('decided_by')
                ->nullable(); // FK ajoutée après création de users, dans la migration PNMG suivante
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_status_history');
    }
};
