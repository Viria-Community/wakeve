<?php

namespace App\Http\Controllers;

use App\Models\CustomRole;
use App\Models\Employee;
use App\Models\User;
use App\Support\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EmployeeAccountController extends Controller
{
    public function edit(Employee $employee)
    {
        Gate::authorize('manageAccount', $employee);

        return view('employees.account', [
            'employee' => $employee->load('user'),
            'roles' => CustomRole::where('company_id', $employee->company_id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('manageAccount', $employee);
        $request->merge([
            'email' => mb_strtolower(trim((string) $request->input('email'))),
        ]);
        DB::transaction(function () use ($request, $employee) {
            $employee = Employee::whereKey($employee->id)->lockForUpdate()->firstOrFail();
            $employee->load('user');
            Gate::authorize('manageAccount', $employee);
            $data = $request->validate([
                'account_exists' => ['required', 'boolean'],
                'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($employee->user_id)],
                'role' => ['required', Rule::in($request->user()->isAdmin() ? ['company_manager', 'agency_manager', 'agent'] : ['agency_manager', 'agent'])],
                'status' => ['required', Rule::in(['active', 'inactive'])],
                'custom_role_id' => ['nullable', 'integer', Rule::exists('custom_roles', 'id')->where('company_id', $employee->company_id)->where(fn ($q) => $q->whereNotNull('scope')->orWhere('base_role', $request->input('role')))],
                'password' => [$employee->user_id ? 'exclude' : 'required', 'confirmed', StrongPassword::rule()],
            ], [
                'email.unique' => 'Cette adresse email est déjà utilisée par un compte.',
                'email.required' => 'L’adresse email du compte est obligatoire.',
                'password.required' => 'Un mot de passe est obligatoire pour créer le compte.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
                'custom_role_id.exists' => 'Choisissez un rôle compatible de cette compagnie.',
            ]);
            if ($request->boolean('account_exists') !== (bool) $employee->user_id) {
                throw ValidationException::withMessages(['email' => 'Ce compte a été modifié entre-temps. Rechargez la page avant de continuer.']);
            }
            $customRole = CustomRole::find($data['custom_role_id'] ?? null);
            $needsAgency = $customRole?->scope ? $customRole->scope !== 'company' : in_array($data['role'], ['agency_manager', 'agent'], true);
            if ($needsAgency && (! $employee->agency || $employee->agency->company_id !== $employee->company_id)) {
                throw ValidationException::withMessages(['role' => 'Rattachez d’abord cet employé à une agence de sa compagnie, ou choisissez un rôle personnalisé de périmètre compagnie.']);
            }
            $attributes = [
                'name' => $employee->fullName(), 'email' => $data['email'], 'role' => $data['role'],
                'status' => $data['status'], 'custom_role_id' => $data['custom_role_id'] ?? null,
            ];
            if ($employee->user) {
                if ($employee->user->email !== $data['email']) {
                    $employee->user->email_verified_at = null;
                }
                $employee->user->update($attributes);
                $employee->update(['status' => $data['status']]);
            } else {
                $account = User::create($attributes + ['company_id' => $employee->company_id, 'password' => $data['password']]);
                $employee->update(['user_id' => $account->id, 'status' => $data['status']]);
            }
        });

        return redirect()->route('employees.show', $employee)->with('status', 'L’employé et ses accès ont été mis à jour.');
    }
}
