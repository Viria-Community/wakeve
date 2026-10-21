<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgencyIsOperational
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->isPlatformMember() || $user->accessScope() === 'company') {
            return $next($request);
        }

        $agency = $user->employee?->agency;

        if (! $agency) {
            abort(403, 'Ce compte doit être rattaché à une agence active avant d’accéder à cette rubrique.');
        }

        if ($agency->company_id !== $user->company_id) {
            abort(403, 'L’agence rattachée à ce compte est invalide.');
        }

        if ($agency->status !== 'active') {
            abort(403, 'Cette agence est désactivée et ne peut plus effectuer d’opérations.');
        }

        return $next($request);
    }
}
