<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class RccmInput
{
    public static function normalize(Request $request): void
    {
        $rccm = $request->input('rccm');

        if (! is_string($rccm)) {
            return;
        }

        $rccm = Str::upper(trim(preg_replace('/\s+/u', ' ', $rccm) ?? $rccm));
        $request->merge(['rccm' => $rccm]);
    }
}
