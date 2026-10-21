<?php

namespace App\Services;

class PermissionCatalog
{
    public static function labels(): array
    {
        return [
            'companies.view' => 'Consulter la compagnie et ses documents',
            'companies.update' => 'Modifier la compagnie et ses documents',
            'agencies.view' => 'Consulter les agences autorisées',
            'agencies.create' => 'Créer une agence',
            'agencies.update' => 'Modifier les agences autorisées',
            'employees.view' => 'Consulter les employés autorisés',
            'employees.create' => 'Ajouter un employé avec son compte',
            'employees.update' => 'Modifier les employés autorisés',
            'fleet.view' => 'Consulter la flotte et ses documents',
            'fleet.create' => 'Ajouter un moyen de transport',
            'fleet.update' => 'Modifier la flotte et ses documents',
            'activity.view' => 'Consulter le journal du périmètre autorisé',
        ];
    }

    public static function roles(): array
    {
        return ['company_manager' => 'Gestionnaire de compagnie', 'agency_manager' => 'Responsable d’agence', 'agent' => 'Agent'];
    }

    public static function groups(): array
    {
        return [
            'company' => [
                'label' => 'Compagnie',
                'description' => 'Informations générales et documents de la compagnie.',
                'permissions' => ['companies.view', 'companies.update'],
            ],
            'agencies' => [
                'label' => 'Agences',
                'description' => 'Consultation et gestion des agences autorisées.',
                'permissions' => ['agencies.view', 'agencies.create', 'agencies.update'],
            ],
            'employees' => [
                'label' => 'Employés',
                'description' => 'Personnel, rattachements et informations professionnelles.',
                'permissions' => ['employees.view', 'employees.create', 'employees.update'],
            ],
            'fleet' => [
                'label' => 'Flotte',
                'description' => 'Moyens de transport et documents associés.',
                'permissions' => ['fleet.view', 'fleet.create', 'fleet.update'],
            ],
            'activity' => [
                'label' => 'Journal d’activité',
                'description' => 'Historique des actions du périmètre autorisé.',
                'permissions' => ['activity.view'],
            ],
        ];
    }

    public static function scopes(): array
    {
        return ['company' => 'Toute la compagnie', 'agency' => 'Agence de rattachement', 'self' => 'Fiche personnelle et agence en consultation'];
    }

    public static function forScope(string $scope): array
    {
        return match ($scope) {
            'company' => array_keys(self::labels()),
            'agency' => ['agencies.view', 'agencies.update', 'employees.view', 'employees.create', 'employees.update', 'activity.view'],
            'self' => ['agencies.view', 'employees.view'],
            default => [],
        };
    }

    public static function allowed(string $role): array
    {
        $read = ['companies.view', 'agencies.view', 'employees.view', 'fleet.view'];

        return match ($role) {
            'company_manager' => array_values(array_diff(array_keys(self::labels()), ['activity.view'])),
            'agency_manager' => [...$read, 'agencies.update', 'employees.create', 'employees.update', 'activity.view'],
            'agent' => $read,
            default => [],
        };
    }
}
