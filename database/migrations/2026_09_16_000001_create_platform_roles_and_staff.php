<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->json('permissions');
            $table->timestamps();
        });

        DB::table('platform_roles')->insert([
            ['name' => 'Agent de validation des compagnies', 'permissions' => json_encode(['companies.view', 'companies.validate']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Agent de suspension', 'permissions' => json_encode(['companies.view', 'companies.suspend']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Agent de contrôle documentaire', 'permissions' => json_encode(['companies.view', 'documents.view', 'documents.validate']), 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Agent de consultation', 'permissions' => json_encode(['companies.view', 'documents.view']), 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('platform_role_id')->nullable()->after('custom_role_id')->constrained()->restrictOnDelete();
        });

        Schema::table('users', fn (Blueprint $table) => $table->string('role', 30)->change());
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'platform_staff')->update(['role' => 'agent', 'platform_role_id' => null]);
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('platform_role_id'));
        Schema::dropIfExists('platform_roles');
    }
};
