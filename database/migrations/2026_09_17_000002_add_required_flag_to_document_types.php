<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->boolean('is_required')->default(false)->after('applicable_to');
        });

        DB::table('document_types')
            ->where('label', "Autorisation d'exploitation")
            ->update(['is_required' => true]);
    }

    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn('is_required');
        });
    }
};
