<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fleet_units', function (Blueprint $table) {
            $table->foreignId('transport_mode_id')->nullable()->constrained()->restrictOnDelete();
        });
        Schema::table('agencies', function (Blueprint $table) {
            // Nullable unique company reference allows multiple secondary agencies, one headquarters.
            $table->foreignId('headquarters_for')->nullable()->unique()->constrained('companies')->restrictOnDelete();
        });
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('agency_id')->nullable()->index();
        });
        DB::table('companies')->whereNull('deleted_at')->orderBy('id')->each(function ($company) {
            $existing = DB::table('agencies')->where('company_id', $company->id)->whereNull('deleted_at')->where('name', 'Siège')->orderBy('id')->first();
            if ($existing) {
                DB::table('agencies')->where('id', $existing->id)->update(['headquarters_for' => $company->id, 'status' => 'active']);

                return;
            }
            DB::table('agencies')->insert([
                'company_id' => $company->id, 'headquarters_for' => $company->id,
                'name' => 'Siège', 'address' => $company->address, 'city' => $company->city,
                'phone' => $company->phone, 'status' => 'active', 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
        // Existing fleet is deliberately left unclassified rather than guessing a mode.
    }

    public function down(): void
    {
        Schema::table('audit_logs', fn (Blueprint $table) => $table->dropColumn('agency_id'));
        Schema::table('agencies', fn (Blueprint $table) => $table->dropConstrainedForeignId('headquarters_for'));
        Schema::table('fleet_units', fn (Blueprint $table) => $table->dropConstrainedForeignId('transport_mode_id'));
    }
};
