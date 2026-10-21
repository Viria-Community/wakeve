<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_roles', fn (Blueprint $table) => $table->string('scope', 20)->nullable());
        Schema::create('role_presets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description');
            $table->string('scope');
            $table->json('permissions');
        });
        $definitions = [
            ['admin', 'Administrateur de la plateforme', 'Examine et valide les compagnies, supervise le référentiel, gère les états et consulte le tableau de bord national.', 'national', []],
            ['company_manager', 'Gestionnaire de compagnie', 'Gère sa compagnie, ses documents, ses agences, ses employés et sa flotte.', 'company', ['companies.view', 'companies.update', 'agencies.view', 'agencies.create', 'agencies.update', 'employees.view', 'employees.create', 'employees.update', 'fleet.view', 'fleet.create', 'fleet.update', 'activity.view']],
            ['agency_manager', 'Responsable d’agence', 'Gère les informations de son agence et les employés qui y sont rattachés.', 'agency', ['agencies.view', 'agencies.update', 'employees.view', 'employees.create', 'employees.update', 'activity.view']],
            ['agent', 'Agent de compagnie', 'Accès en consultation limité à son rattachement et à sa fiche employé.', 'self', ['agencies.view', 'employees.view']],
        ];
        foreach ($definitions as [$code, $name, $description, $scope, $permissions]) {
            DB::table('role_presets')->insert(compact('code', 'name', 'description', 'scope') + ['permissions' => json_encode($permissions)]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_presets');
        Schema::table('custom_roles', fn (Blueprint $table) => $table->dropColumn('scope'));
    }
};
