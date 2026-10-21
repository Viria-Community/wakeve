<?php

namespace App\Support;

use Illuminate\Http\Request;

class GeographyInput
{
    public static function normalize(Request $request): void
    {
        $city = $request->input('city');

        if ($city === '__other__') {
            $city = $request->input('city_custom');
        }

        if (is_string($city)) {
            $city = trim($city);
        }

        $request->merge(['city' => $city === '' ? null : $city]);
    }
}
