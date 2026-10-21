<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('type', 50); // ex. bus, pirogue, bateau... (champ libre, cf. discussion validée)
            $table->string('registration_number', 50)->nullable()->unique();
            $table->unsignedSmallInteger('capacity');
            $table->enum('availability', ['available', 'unavailable'])->default('available');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // CHECK constraint uniquement en MySQL/MariaDB : SQLite (utilisé par
        // défaut en environnement de test avec RefreshDatabase) n'accepte
        // pas ALTER TABLE ... ADD CONSTRAINT ... CHECK après la création
        // de la table — la contrainte devrait être déclarée dans le
        // CREATE TABLE, ce que Laravel ne permet pas nativement via
        // Blueprint pour SQLite. La validation capacity > 0 reste de
        // toute façon appliquée côté Form Request (couche HTTP).
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE fleet_units ADD CONSTRAINT fleet_units_capacity_check CHECK (capacity > 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_units');
    }
};
