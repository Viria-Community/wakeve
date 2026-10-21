<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['companies', 'agencies'] as $table) {
            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->string('province', 2)->nullable()->index());
            foreach (config('geography') as $code => $province) {
                $names = array_map(fn ($city) => Str::lower(Str::ascii($city)), $province['cities']);
                DB::table($table)->whereNotNull('city')->orderBy('id')->chunkById(200, function ($rows) use ($table, $code, $names) {
                    foreach ($rows as $row) {
                        if (in_array(Str::lower(Str::ascii(trim($row->city))), $names, true)) {
                            DB::table($table)->where('id', $row->id)->update(['province' => $code]);
                        }
                    }
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['companies', 'agencies'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropIndex(['province']);
                $blueprint->dropColumn('province');
            });
        }
    }
};
