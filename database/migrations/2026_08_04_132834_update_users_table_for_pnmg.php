<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'company_manager', 'agency_manager', 'agent'])
                ->after('password');

            $table->foreignId('company_id')
                ->nullable()
                ->after('role')
                ->constrained('companies')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('agency_id')
                ->nullable()
                ->after('company_id')
                ->constrained('agencies')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->enum('status', ['active', 'inactive'])
                ->default('active')
                ->after('agency_id');
        });

        // Liaison de la clé étrangère différée pour l'historique des statuts
        Schema::table('company_status_history', function (Blueprint $table) {
            $table->foreign('decided_by', 'csh_decided_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('company_status_history', function (Blueprint $table) {
            $table->dropForeign('csh_decided_by_fk');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agency_id');
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn(['role', 'status']);
        });
    }
};
