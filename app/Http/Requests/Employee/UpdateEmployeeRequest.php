<?php

namespace App\Http\Requests\Employee;

use App\Models\Agency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('employee')) ?? false;
    }

    public function rules(): array
    {
        return [
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'last_name' => ['required', 'string', 'max:100'],
            'first_name' => ['required', 'string', 'max:100'],
            'internal_role' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $employee = $this->route('employee');
            if ($employee->user?->hasFreeRole() && $employee->user->accessScope() !== 'company' && ! $this->filled('agency_id')) {
                $validator->errors()->add('agency_id', 'Une agence est obligatoire pour le périmètre de ce rôle.');
            }

            if (in_array($employee->user?->role, ['agency_manager', 'agent'], true) && ! ($employee->user?->hasFreeRole() && $employee->user->accessScope() === 'company') && ! $this->filled('agency_id')) {
                $validator->errors()->add('agency_id', 'Une agence est obligatoire pour ce rôle.');
            }

            if (! $this->filled('agency_id')) {
                return;
            }

            $agency = Agency::find($this->integer('agency_id'));

            if (! $agency || $agency->company_id !== $employee->company_id) {
                $validator->errors()->add('agency_id', 'L’agence doit appartenir à la compagnie de l’employé.');
            }

            if ($this->user()->accessScope() === 'agency' && $this->user()->employee?->agency_id !== $agency?->id) {
                $validator->errors()->add('agency_id', 'Vous ne pouvez utiliser que votre agence.');
            }
        }];
    }
}
