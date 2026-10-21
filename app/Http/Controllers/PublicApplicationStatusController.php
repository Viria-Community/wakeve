<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PublicApplicationStatusController extends Controller
{
    public function create(): View
    {
        return view('public.application-status');
    }

    public function show(Request $request): View
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:50', 'regex:/^REF-GAB-\d{4}-[A-Z0-9]{8}$/i'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $reference = mb_strtoupper(trim($data['reference']));
        $email = mb_strtolower(trim($data['email']));
        $throttleKey = 'public-application-status:'.hash('sha256', $request->ip().'|'.$email);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return view('public.application-status', [
                'lookupError' => 'Trop de tentatives. Réessayez dans quelques minutes.',
            ]);
        }

        RateLimiter::hit($throttleKey, 900);

        $company = Company::query()
            ->where('application_reference', $reference)
            ->whereHas('users', fn ($query) => $query
                ->where('role', 'company_manager')
                ->where('email', $email))
            ->first();

        if (! $company) {
            return view('public.application-status', [
                'lookupError' => 'Aucun dossier ne correspond aux informations saisies. Vérifiez le numéro et l’e-mail du gestionnaire.',
            ]);
        }

        return view('public.application-status', compact('company'));
    }
}
