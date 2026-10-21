<?php

namespace App\Http\Middleware;

use App\Enums\CompanyStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsOperational
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isPlatformMember()) {
            return $next($request);
        }

        if ($user?->company?->status === CompanyStatus::Active) {
            return $next($request);
        }

        if ($user?->role === 'company_manager' && $user->company_id !== null) {
            return redirect()->route('application.status')
                ->withErrors(['company' => 'Les opérations sont interrompues pour cette compagnie.']);
        }

        abort(403, 'La compagnie n’est pas autorisée à effectuer des opérations.');
    }
}
