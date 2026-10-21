<?php

namespace App\Http\Requests\FleetUnit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFleetUnitRequest extends FormRequest
{
    use ValidatesTransportMode;

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('fleet_unit')) ?? false;
    }

    public function rules(): array
    {
        $fleetUnit = $this->route('fleet_unit');

        return [
            'transport_mode_id' => ['required', 'integer', 'exists:transport_modes,id'],
            'type' => ['required', 'string', 'max:50'],
            'registration_number' => ['nullable', 'string', 'max:50', Rule::unique('fleet_units', 'registration_number')->ignore($fleetUnit)],
            'capacity' => ['required', 'integer', 'min:1', 'max:65535'],
            'availability' => ['required', Rule::in(['available', 'unavailable'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
