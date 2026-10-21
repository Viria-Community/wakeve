<?php

namespace App\Http\Controllers;

use App\Models\PlatformRole;
use App\Models\User;
use App\Services\AuditLogger;
use App\Support\StrongPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlatformStaffController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('platform-staff.index', [
            'staff' => User::query()->where('role', 'platform_staff')->with('platformRole')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return $this->form(new User);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $data = $this->validated($request);
        $data['role'] = 'platform_staff';
        $data['company_id'] = null;
        $data['custom_role_id'] = null;
        $user = User::create($data);
        AuditLogger::record('creation_personnel_pnmg', 'Création d’un employé de la plateforme', $user);

        return redirect()->route('platform-staff.index')->with('status', 'L’employé PNMG a été créé et peut se connecter.');
    }

    public function edit(Request $request, User $platformStaff): View
    {
        $this->authorizeAdmin($request);
        $this->ensurePlatformStaff($platformStaff);

        return $this->form($platformStaff);
    }

    public function update(Request $request, User $platformStaff): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $this->ensurePlatformStaff($platformStaff);
        $data = $this->validated($request, $platformStaff);
        if (! $request->filled('password')) {
            unset($data['password']);
        }
        $platformStaff->update($data);
        AuditLogger::record('modification_personnel_pnmg', 'Mise à jour d’un employé de la plateforme', $platformStaff);

        return redirect()->route('platform-staff.index')->with('status', 'Le compte PNMG a été mis à jour.');
    }

    private function form(User $user): View
    {
        return view('platform-staff.form', [
            'staffMember' => $user,
            'roles' => PlatformRole::query()->orderBy('name')->get(),
        ]);
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'platform_role_id' => ['required', 'integer', 'exists:platform_roles,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => [$user?->exists ? 'nullable' : 'required', 'confirmed', StrongPassword::rule()],
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403);
    }

    private function ensurePlatformStaff(User $user): void
    {
        abort_unless($user->isPlatformStaff(), 404);
    }
}
