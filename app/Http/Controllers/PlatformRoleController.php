<?php

namespace App\Http\Controllers;

use App\Models\PlatformRole;
use App\Services\PlatformPermissionCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PlatformRoleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('platform-roles.index', [
            'roles' => PlatformRole::query()->withCount('users')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('platform-roles.form', ['role' => new PlatformRole]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        PlatformRole::create($this->validated($request));

        return redirect()->route('platform-roles.index')->with('status', 'Le rôle PNMG a été créé.');
    }

    public function edit(Request $request, PlatformRole $platformRole): View
    {
        $this->authorizeAdmin($request);

        return view('platform-roles.form', ['role' => $platformRole]);
    }

    public function update(Request $request, PlatformRole $platformRole): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $platformRole->update($this->validated($request, $platformRole));

        return redirect()->route('platform-roles.index')->with('status', 'Les permissions du rôle PNMG ont été mises à jour.');
    }

    private function validated(Request $request, ?PlatformRole $role = null): array
    {
        $request->merge(['permissions' => $request->input('permissions', [])]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('platform_roles')->ignore($role)],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::in(PlatformPermissionCatalog::all())],
        ]);

        foreach ([
            'companies.validate' => 'companies.view',
            'companies.suspend' => 'companies.view',
            'documents.validate' => 'documents.view',
        ] as $operation => $required) {
            if (in_array($operation, $data['permissions'], true) && ! in_array($required, $data['permissions'], true)) {
                throw ValidationException::withMessages([
                    'permissions' => 'La permission de consultation correspondante est obligatoire pour autoriser cette action.',
                ]);
            }
        }

        return $data;
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403);
    }
}
