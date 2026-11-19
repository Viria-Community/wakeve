<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->string('vehicle_category', 20)->default('basique')->after('fare_class');
        });
    }

    public function down(): void
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->dropColumn('vehicle_category');
        });
    }
};
