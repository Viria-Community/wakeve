<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->foreignId('origin_place_id')
                ->nullable()
                ->after('line_id')
                ->constrained('places')
                ->restrictOnDelete();

            $table->foreignId('destination_place_id')
                ->nullable()
                ->after('origin_place_id')
                ->constrained('places')
                ->restrictOnDelete();

            $table->index(
                ['line_id', 'origin_place_id', 'destination_place_id', 'fare_class'],
                'fares_route_lookup_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('fares', function (Blueprint $table) {
            $table->dropIndex('fares_route_lookup_index');
            $table->dropConstrainedForeignId('destination_place_id');
            $table->dropConstrainedForeignId('origin_place_id');
        });
    }
};
