<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $seen = [];

        DB::table('companies')->select(['id', 'rccm'])->orderBy('id')->chunkById(100, function ($companies) use (&$seen): void {
            foreach ($companies as $company) {
                $normalized = mb_strtoupper(trim(preg_replace('/\s+/u', ' ', (string) $company->rccm) ?? ''), 'UTF-8');

                if ($normalized === '') {
                    throw new RuntimeException("La compagnie #{$company->id} doit recevoir un RCCM avant la migration.");
                }

                if (isset($seen[$normalized])) {
                    throw new RuntimeException("Le RCCM {$normalized} est partagé par les compagnies #{$seen[$normalized]} et #{$company->id}.");
                }

                $seen[$normalized] = $company->id;
                DB::table('companies')->where('id', $company->id)->update(['rccm' => $normalized]);
            }
        });

        Schema::table('companies', function (Blueprint $table): void {
            $table->string('rccm', 50)->nullable(false)->change();
            $table->unique('rccm');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropUnique(['rccm']);
            $table->string('rccm', 50)->nullable()->change();
        });
    }
};
