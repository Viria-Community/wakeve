<?php

namespace App\Http\Requests\Line;

use App\Enums\CompanyStatus;
use App\Models\Company;
use App\Models\Line;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('line')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $places = collect($this->input('places', []))
            ->values()
            ->map(fn ($place, $index) => [
                'place_id' => data_get($place, 'place_id'),
                'stop_order' => $index,
            ])
            ->all();

        $this->merge([
            'company_id' => $this->user()->isAdmin()
                ? $this->input('company_id')
                : $this->user()->company_id,
            'places' => $places,
        ]);
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
            'places.required' => 'Au moins deux points desservis sont obligatoires.',
            'places.min' => 'Au moins deux points desservis sont obligatoires.',
            'places.*.place_id.required' => 'Chaque point desservi doit être choisi.',
            'places.*.place_id.exists' => 'Un point desservi sélectionné n\'existe pas.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $company = Company::find($this->input('company_id'));

            if (! $company) {
                $validator->errors()->add('company_id', 'La compagnie n\'existe pas.');
                return;
            }

            if (! in_array($company->status, [CompanyStatus::Validated, CompanyStatus::Active], true)) {
                $validator->errors()->add('company_id', 'La compagnie doit être validée ou active.');
            }

            if (! $company->transportModes()->whereKey($this->input('transport_mode_id'))->exists()) {
                $validator->errors()->add('transport_mode_id', 'Ce mode de transport n\'est pas pratiqué par la compagnie.');
            }

            $origin = (string) $this->input('origin_place_id');
            $destination = (string) $this->input('destination_place_id');

            if ($origin !== '' && $origin === $destination) {
                $validator->errors()->add('destination_place_id', 'L\'origine et la destination doivent être différentes.');
            }

            $placeIds = collect($this->input('places', []))
                ->pluck('place_id')
                ->map(fn ($id) => (string) $id)
                ->all();

            if (count($placeIds) !== count(array_unique($placeIds))) {
                $validator->errors()->add('places', 'Un lieu ne peut apparaître qu\'une seule fois dans les points desservis.');
            }

            if ($origin !== '' && ! in_array($origin, $placeIds, true)) {
                $validator->errors()->add('origin_place_id', 'L\'origine doit figurer dans les points desservis.');
            }

            if ($destination !== '' && ! in_array($destination, $placeIds, true)) {
                $validator->errors()->add('destination_place_id', 'La destination doit figurer dans les points desservis.');
            }
        });
    }
}
