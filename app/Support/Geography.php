<?php

namespace App\Support;

class Geography
{
    public static function available(): array
    {
        $quartiers = config('quartiers', []);
        $resultat = [];

        foreach (config('geography') as $code => $province) {
            $villes = array_values(array_filter(
                $province['cities'],
                fn ($ville) => ! empty($quartiers[$ville] ?? [])
            ));

            if ($villes !== []) {
                $resultat[$code] = array_merge($province, ['cities' => $villes]);
            }
        }

        return $resultat;
    }

    public static function citiesAvailable(?string $code): array
    {
        return self::available()[$code]['cities'] ?? [];
    }
}
