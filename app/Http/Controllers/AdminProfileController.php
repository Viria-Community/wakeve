<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateAdminProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('profile.admin', [
            'user' => $user,
            'profilePhone' => $user->company?->phone,
        ]);
    }

    public function update(UpdateAdminProfileRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email']);

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        $request->user()->update($data);

        return redirect()->route('profile.edit')
            ->with('status', 'Votre profil a été mis à jour.');
    }
}
