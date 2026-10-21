<?php

namespace App\Http\Requests\FleetUnit;

use App\Models\Company;
use Illuminate\Validation\Validator;

trait ValidatesTransportMode
{
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $companyId = $this->route('fleet_unit')?->company_id
                ?? ($this->user()->isAdmin() ? $this->input('company_id') : $this->user()->company_id);
            $company = Company::find($companyId);
            $mode = $company?->transportModes()->where('transport_modes.id', $this->input('transport_mode_id'))->first();
            if (! $mode) {
                $validator->errors()->add('transport_mode_id', 'Sélectionnez un mode de transport déclaré par cette compagnie.');
            } elseif (! in_array($this->input('type'), config('fleet.types.'.$mode->label, []), true)) {
                $validator->errors()->add('type', 'Ce type d’unité ne correspond pas au mode de transport sélectionné.');
            }
        }];
    }
}
