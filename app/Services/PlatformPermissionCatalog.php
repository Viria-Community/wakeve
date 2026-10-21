<?php

namespace App\Services;

class PlatformPermissionCatalog
{
    public static function labels(): array
    {
        return [
            'companies.view' => 'Consulter les compagnies',
            'companies.validate' => 'Valider ou refuser les compagnies',
            'companies.suspend' => 'Suspendre ou réactiver les compagnies',
            'documents.view' => 'Consulter les documents',
            'documents.validate' => 'Valider ou refuser les documents',
            'audit.view' => 'Consulter le journal d’activité',
        ];
    }

    public static function all(): array
    {
        return array_keys(self::labels());
    }

    public static function groups(): array
    {
        return [
            'companies' => [
                'label' => 'Compagnies',
                'description' => 'Consultation, validation et suspension des compagnies.',
                'permissions' => ['companies.view', 'companies.validate', 'companies.suspend'],
            ],
            'documents' => [
                'label' => 'Documents',
                'description' => 'Consultation et contrôle des pièces déposées.',
                'permissions' => ['documents.view', 'documents.validate'],
            ],
            'audit' => [
                'label' => 'Journal d’activité',
                'description' => 'Accès à la traçabilité des opérations de la plateforme.',
                'permissions' => ['audit.view'],
            ],
        ];
    }
}
