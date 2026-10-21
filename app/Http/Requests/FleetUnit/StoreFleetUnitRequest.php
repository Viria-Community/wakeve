<?php

namespace App\Http\Requests\FleetUnit;

use App\Models\FleetUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFleetUnitRequest extends FormRequest
{
    use ValidatesTransportMode;

    public function authorize(): bool
    {
        return $this->user()?->can('create', FleetUnit::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => [$this->user()->isAdmin() ? 'required' : 'nullable', 'integer', 'exists:companies,id'],
            'transport_mode_id' => ['required', 'integer', 'exists:transport_modes,id'],
            'type' => ['required', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50', 'unique:fleet_units,registration_number'],
            'capacity' => ['required', 'integer', 'min:1', 'max:65535'],
            'availability' => ['required', Rule::in(['available', 'unavailable'])],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'La compagnie est obligatoire.',
            'type.required' => 'Le type de l’unité est obligatoire.',
            'registration_number.unique' => 'Ce numéro d’immatriculation est déjà utilisé.',
            'capacity.required' => 'La capacité est obligatoire.',
            'capacity.min' => 'La capacité doit être supérieure à zéro.',
        ];
    }
}
