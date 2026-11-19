<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->string('travel_class', 20)->default('standard')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->dropColumn('travel_class');
        });
    }
};
