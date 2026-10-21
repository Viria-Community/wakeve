<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CustomRole;
use App\Models\Employee;
use App\Models\RolePreset;
use App\Models\User;
use App\Services\PermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomRoleController extends Controller
{
    private function authorizeManager(Request $request, ?int $companyId = null): void
    {
        abort_unless($request->user()->canManageRoles(), 403);
        abort_unless($request->user()->isAdmin() || $companyId === null || $companyId === $request->user()->company_id, 403);
    }

    public function index(Request $request)
    {
        $this->authorizeManager($request);

        return view('roles.index', [
            'presets' => RolePreset::query()
                ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('scope', '!=', 'national'))
                ->orderBy('id')
                ->get(),
            'roles' => CustomRole::with('company')->withCount('users')->when(! $request->user()->isAdmin(), fn ($q) => $q->where('company_id', $request->user()->company_id))->orderBy('name')->paginate(20),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorizeManager($request);

        return $this->form($request, new CustomRole);
    }

    public function edit(Request $request, CustomRole $role)
    {
        $this->authorizeManager($request, $role->company_id);

        return $this->form($request, $role);
    }

    private function form(Request $request, CustomRole $role)
    {
        return view('roles.form', [
            'presets' => RolePreset::query()
                ->where('scope', '!=', 'national')
                ->where('code', '!=', 'admin')
                ->orderBy('id')
                ->get(),
            'role' => $role,
            'companies' => $request->user()->isAdmin() ? Company::orderBy('legal_name')->get() : collect(),
        ]);
    }

    private function validated(Request $request, ?CustomRole $role = null): array
    {
        $companyId = $role?->company_id ?? ($request->user()->isAdmin() ? $request->input('company_id') : $request->user()->company_id);
        $free = $request->has('scope') || $role?->scope !== null;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('custom_roles')->where('company_id', $companyId)->ignore($role?->id)],
            'company_id' => [$role || ! $request->user()->isAdmin() ? 'nullable' : 'required', 'integer', 'exists:companies,id'],
            'scope' => [$free ? 'required' : 'nullable', Rule::in(array_keys(PermissionCatalog::scopes()))],
            'base_role' => [$free ? 'exclude' : 'required', Rule::in($role ? [$role->base_role] : array_keys(PermissionCatalog::roles()))],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::in($free ? PermissionCatalog::forScope(is_string($request->input('scope')) ? $request->input('scope') : '') : PermissionCatalog::allowed(is_string($request->input('base_role')) ? $request->input('base_role') : ''))],
        ]);
        foreach (['companies', 'agencies', 'employees', 'fleet'] as $resource) {
            if (array_intersect([$resource.'.create', $resource.'.update'], $data['permissions']) && ! in_array($resource.'.view', $data['permissions'], true)) {
                throw ValidationException::withMessages(['permissions' => 'La consultation doit être autorisée pour pouvoir ajouter ou modifier les éléments du module.']);
            }
        }

        if ($free && $data['scope'] !== 'company' && $role && User::where('custom_role_id', $role->id)->whereDoesntHave('employee.agency', fn ($q) => $q->where('company_id', $companyId))->exists()) {
            throw ValidationException::withMessages(['scope' => 'Tous les comptes associés doivent être rattachés à une agence de cette compagnie avant ce changement de périmètre.']);
        }

        return ['company_id' => $companyId, 'name' => $data['name'], 'base_role' => $free ? 'custom' : $data['base_role'], 'scope' => $free ? $data['scope'] : null, 'permissions' => $data['permissions']];
    }

    public function store(Request $request)
    {
        $this->authorizeManager($request);
        // An empty selection is a valid role without business access.
        $request->merge(['permissions' => $request->input('permissions', [])]);
        CustomRole::create($this->validated($request));

        return redirect()->route('roles.index')->with('status', 'Le rôle personnalisé a été créé.');
    }

    public function update(Request $request, CustomRole $role)
    {
        $this->authorizeManager($request, $role->company_id);
        $request->merge(['permissions' => $request->input('permissions', [])]);
        $role->update($this->validated($request, $role));

        return redirect()->route('roles.index')->with('status', 'Les permissions ont été mises à jour pour tous les comptes utilisant ce rôle.');
    }

    public function assign(Request $request, Employee $employee)
    {
        $this->authorizeManager($request, $employee->company_id);
        abort_unless($employee->user && ! $employee->user->isAdmin() && $employee->user_id !== $request->user()->id, 403);
        abort_unless($request->user()->isAdmin() || $employee->user->role !== 'company_manager', 403);
        $data = $request->validate([
            'custom_role_id' => ['nullable', 'integer', Rule::exists('custom_roles', 'id')->where('company_id', $employee->company_id)->where(fn ($q) => $q->whereNotNull('scope')->orWhere('base_role', $employee->user->role))],
        ]);
        $role = CustomRole::find($data['custom_role_id'] ?? null);
        if (! $role && in_array($employee->user->role, ['agency_manager', 'agent'], true) && ! $employee->agency_id) {
            throw ValidationException::withMessages(['custom_role_id' => 'Rattachez cet employé à une agence avant de revenir aux permissions standard.']);
        }
        if ($role?->scope && $role->scope !== 'company' && (! $employee->agency || $employee->agency->company_id !== $employee->company_id)) {
            throw ValidationException::withMessages(['custom_role_id' => 'Rattachez cet employé à une agence de sa compagnie avant d’attribuer ce rôle.']);
        }
        DB::transaction(function () use ($employee, $data) {
            $employee->user->update(['custom_role_id' => $data['custom_role_id'] ?? null]);
        });

        return redirect()->route('employees.show', $employee)->with('status', 'Les permissions du compte ont été mises à jour.');
    }
}
