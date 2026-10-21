<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Préserve les anciens rattachements avant de supprimer la source redondante.
        DB::table('users')
            ->whereNotNull('agency_id')
            ->whereIn('role', ['agency_manager', 'agent'])
            ->orderBy('id')
            ->each(function (object $user): void {
                $employee = DB::table('employees')->where('user_id', $user->id)->first();

                if ($employee) {
                    if ($employee->agency_id === null) {
                        DB::table('employees')->where('id', $employee->id)->update(['agency_id' => $user->agency_id]);
                    }

                    return;
                }

                $agency = DB::table('agencies')->where('id', $user->agency_id)->first();
                if (! $agency) {
                    return;
                }

                $parts = preg_split('/\s+/', trim((string) $user->name), 2) ?: [];
                DB::table('employees')->insert([
                    'company_id' => $user->company_id ?? $agency->company_id,
                    'agency_id' => $agency->id,
                    'user_id' => $user->id,
                    'last_name' => $parts[1] ?? $parts[0] ?? 'Utilisateur',
                    'first_name' => $parts[0] ?? 'Compte',
                    'internal_role' => $user->role,
                    'status' => $user->status === 'inactive' ? 'inactive' : 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        DB::table('users')->where('status', 'pending')->update(['status' => 'active']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agency_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agency_id')
                ->nullable()
                ->after('company_id')
                ->constrained('agencies')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('status', ['pending', 'active', 'inactive'])->default('active')->change();
        });

        DB::table('employees')
            ->whereNotNull('user_id')
            ->whereNotNull('agency_id')
            ->each(function (object $employee): void {
                DB::table('users')->where('id', $employee->user_id)->update(['agency_id' => $employee->agency_id]);
            });
    }
};
