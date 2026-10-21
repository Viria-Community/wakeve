<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L’adresse email est obligatoire.',
            'email.email' => 'L’adresse email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Trop de tentatives. Réessayez dans '.RateLimiter::availableIn($throttleKey).' seconde(s).',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Les identifiants sont incorrects ou le compte est désactivé.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        if ($request->user()->status === 'inactive') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Les identifiants sont incorrects ou le compte est désactivé.',
            ]);
        }

        $company = $request->user()->company;

        if ($request->user()->role === 'company_manager' && $company?->status?->value !== 'active') {
            return redirect()->route('application.status');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Vous êtes maintenant déconnecté.');
    }
}
