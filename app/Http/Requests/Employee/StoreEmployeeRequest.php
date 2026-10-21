<?php

namespace App\Http\Requests\Employee;

use App\Models\Agency;
use App\Models\CustomRole;
use App\Models\Employee;
use App\Support\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Employee::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => [$this->user()->isAdmin() ? 'required' : 'nullable', 'integer', 'exists:companies,id'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'internal_role' => ['nullable', 'string', 'max:100'],
            'account_email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'account_role' => ['required', Rule::in($this->allowedAccountRoles())],
            'custom_role_id' => ['nullable', 'integer', Rule::exists('custom_roles', 'id')
                ->where('company_id', $this->user()->isAdmin() ? $this->input('company_id') : $this->user()->company_id)
                ->where(fn ($q) => $q->whereNotNull('scope')->orWhere('base_role', $this->input('account_role')))],
            'password' => ['required', 'confirmed', StrongPassword::rule()],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('account_email')) {
            $this->merge([
                'account_email' => mb_strtolower(trim((string) $this->input('account_email'))),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'account_email.required' => 'L’adresse email du compte est obligatoire.',
            'account_email.email' => 'Saisissez une adresse email valide.',
            'account_email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $customRole = null;
            if (! $validator->errors()->has('custom_role_id') && $this->filled('custom_role_id')) {
                $customRole = CustomRole::find($this->integer('custom_role_id'));
                if ($customRole?->scope && $customRole->scope !== 'company' && ! $this->filled('agency_id')) {
                    $validator->errors()->add('agency_id', 'Une agence est obligatoire pour le périmètre de ce rôle.');
                }
            }
            if ($this->filled('custom_role_id') && ! $this->user()->canManageRoles()) {
                $validator->errors()->add('custom_role_id', 'L’attribution des rôles personnalisés est réservée à l’administrateur et au gestionnaire principal de la compagnie.');
            }
            if (in_array($this->input('account_role'), ['agency_manager', 'agent'], true)
                && $customRole?->scope !== 'company'
                && ! $this->filled('agency_id')) {
                $validator->errors()->add('agency_id', 'Une agence est obligatoire pour ce rôle.');
            }

            if (! $this->filled('agency_id')) {
                return;
            }

            $companyId = $this->user()->isAdmin()
                ? $this->integer('company_id')
                : $this->user()->company_id;

            $agency = Agency::find($this->integer('agency_id'));

            if (! $agency || $agency->company_id !== $companyId) {
                $validator->errors()->add('agency_id', 'L’agence doit appartenir à la compagnie sélectionnée.');
            }

            if ($this->user()->accessScope() === 'agency' && $this->user()->employee?->agency_id !== $agency?->id) {
                $validator->errors()->add('agency_id', 'Vous ne pouvez rattacher un employé qu’à votre agence.');
            }
        }];
    }

    protected function allowedAccountRoles(): array
    {
        return match ($this->user()->role) {
            'admin' => ['company_manager', 'agency_manager', 'agent'],
            'company_manager' => ['agency_manager', 'agent'],
            default => ['agent'],
        };
    }
}
