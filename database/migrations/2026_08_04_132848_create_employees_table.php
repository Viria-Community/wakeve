<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('agency_id')
                ->nullable()
                ->constrained('agencies')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->nullable()
                ->unique() // relation 1-1 optionnelle
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('last_name', 100);
            $table->string('first_name', 100);
            $table->string('internal_role', 100)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
