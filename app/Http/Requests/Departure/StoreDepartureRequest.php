<?php

namespace App\Http\Requests\Departure;

use App\Models\Company;
use App\Models\Departure;
use App\Models\FleetUnit;
use App\Models\Line;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Departure::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'line_id' => ['required', 'integer', 'exists:lines,id'],
            'departure_date' => ['required', 'date', 'after_or_equal:' . now()->toDateString()],
            'departure_time' => ['required', 'date_format:H:i'],
            'fleet_unit_id' => ['required', 'integer', 'exists:fleet_units,id'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'line_id.required' => 'La ligne est obligatoire.',
            'line_id.exists' => 'La ligne sélectionnée n\'existe pas.',
            'departure_date.required' => 'La date de départ est obligatoire.',
            'departure_date.date' => 'La date de départ doit être une date valide.',
            'departure_date.after_or_equal' => 'La date de départ ne peut pas être dans le passé.',
            'departure_time.required' => 'L\'heure de départ est obligatoire.',
            'departure_time.date_format' => 'L\'heure de départ doit être au format HH:MM.',
            'fleet_unit_id.required' => 'Le véhicule est obligatoire.',
            'fleet_unit_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
        ];
    }

    public function after(): array
    {
        return [
            function (\Illuminate\Validation\Validator $validator): void {
                $lineId = $this->input('line_id');
                $line = Line::find($lineId);

                if (!$line) {
                    $validator->errors()->add('line_id', 'La ligne n\'existe pas.');
                    return;
                }

                if (!$line->isActive()) {
                    $validator->errors()->add('line_id', 'La ligne doit être active.');
                }

                $companyId = $this->user()->isAdmin()
                    ? $line->company_id
                    : $this->user()->company_id;

                if ($line->company_id !== $companyId) {
                    $validator->errors()->add('line_id', 'Vous ne pouvez pas gérer cette ligne.');
                }

                $fleetUnit = FleetUnit::find($this->input('fleet_unit_id'));
                if (!$fleetUnit) {
                    $validator->errors()->add('fleet_unit_id', 'Le véhicule n\'existe pas.');
                    return;
                }

                if ($fleetUnit->company_id !== $companyId) {
                    $validator->errors()->add('fleet_unit_id', 'Ce véhicule n\'appartient pas à votre compagnie.');
                }

                if ($fleetUnit->status !== 'active') {
                    $validator->errors()->add('fleet_unit_id', 'Le véhicule doit être actif.');
                }

                if ($fleetUnit->availability !== 'available') {
                    $validator->errors()->add('fleet_unit_id', 'Le véhicule doit être disponible.');
                }

                $agencyId = $this->input('agency_id');
                if ($agencyId) {
                    $agency = \App\Models\Agency::find($agencyId);
                    if ($agency && $agency->company_id !== $companyId) {
                        $validator->errors()->add('agency_id', 'Cette agence n\'appartient pas à votre compagnie.');
                    }
                }
            },
        ];
    }
}
