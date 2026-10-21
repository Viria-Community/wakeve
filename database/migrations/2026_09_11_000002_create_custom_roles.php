<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->string('base_role', 30);
            $table->json('permissions');
            $table->timestamps();
            $table->unique(['company_id', 'name']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('custom_role_id')->nullable()->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('custom_role_id'));
        Schema::dropIfExists('custom_roles');
    }
};
