<?php

namespace App\Http\Requests\Line;

use App\Models\Company;
use App\Models\Line;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Line::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'label' => ['required', 'string', 'max:150'],
            'transport_mode_id' => ['required', 'integer', 'exists:transport_modes,id'],
            'origin_place_id' => ['required', 'integer', 'exists:places,id'],
            'destination_place_id' => ['required', 'integer', 'exists:places,id'],
            'places' => ['required', 'array', 'min:2'],
            'places.*.place_id' => ['required', 'integer', 'exists:places,id'],
            'places.*.stop_order' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'La compagnie est obligatoire.',
            'label.required' => 'Le libellé de la ligne est obligatoire.',
            'transport_mode_id.required' => 'Le mode de transport est obligatoire.',
            'transport_mode_id.exists' => 'Le mode de transport sélectionné n\'existe pas.',
            'origin_place_id.required' => 'L\'origine est obligatoire.',
            'origin_place_id.exists' => 'L\'origine sélectionnée n\'existe pas.',
            'destination_place_id.required' => 'La destination est obligatoire.',
            'destination_place_id.exists' => 'La destination sélectionnée n\'existe pas.',
            'places.required' => 'Au moins deux points de desserte sont obligatoires.',
            'places.min' => 'Au moins deux points de desserte sont obligatoires.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $places = $this->input('places', []);
        usort($places, fn ($a, $b) => ($a['stop_order'] ?? 0) - ($b['stop_order'] ?? 0));
        $this->merge(['places' => array_values($places)]);
    }

    public function after(): array
    {
        return [
            function (): void {
                $companyId = $this->route('company_id')
                    ?? ($this->user()->isAdmin() ? $this->input('company_id') : $this->user()->company_id);
                $company = Company::find($companyId);

                if (!$company) {
                    $this->errors()->add('company_id', 'La compagnie n\'existe pas.');
                    return;
                }

                if (!$company->status->in([Company::STATUS_VALIDATED, Company::STATUS_ACTIVE])) {
                    $this->errors()->add('company_id', 'La compagnie doit être validée ou active.');
                }

                $mode = $company->transportModes()->whereKey($this->input('transport_mode_id'))->first();
                if (!$mode) {
                    $this->errors()->add('transport_mode_id', 'Ce mode de transport n\'est pas pratiqué par la compagnie.');
                }

                if ($this->input('origin_place_id') === $this->input('destination_place_id')) {
                    $this->errors()->add('destination_place_id', 'L\'origine et la destination doivent être différentes.');
                }

                $places = $this->input('places', []);
                $placeIds = array_column($places, 'place_id');
                if (count($placeIds) !== count(array_unique($placeIds))) {
                    $this->errors()->add('places', 'Un lieu ne peut apparaître qu\'une seule fois dans la liste.');
                }

                if (count($places) < 2) {
                    $this->errors()->add('places', 'Au moins deux points de desserte sont obligatoires.');
                }

                $hasOrigin = in_array($this->input('origin_place_id'), $placeIds, true);
                $hasDestination = in_array($this->input('destination_place_id'), $placeIds, true);
                if (!$hasOrigin) {
                    $this->errors()->add('origin_place_id', 'L\'origine doit figurer dans la liste des points desservis.');
                }
                if (!$hasDestination) {
                    $this->errors()->add('destination_place_id', 'La destination doit figurer dans la liste des points desservis.');
                }

                $stopOrders = array_column($places, 'stop_order');
                sort($stopOrders);
                $expectedOrders = range(0, count($places) - 1);
                if ($stopOrders !== $expectedOrders) {
                    $this->errors()->add('places', 'Les ordres de desserte doivent être consécutifs à partir de 0.');
                }
            },
        ];
    }
}
