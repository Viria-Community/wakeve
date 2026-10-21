<?php

namespace App\Http\Requests\Company;

use App\Models\Company;
use App\Rules\LocalityInProvince;
use App\Support\GeographyInput;
use App\Support\RccmInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCompanyRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        GeographyInput::normalize($this);
        RccmInput::normalize($this);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('company')) ?? false;
    }

    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'legal_name' => ['required', 'string', 'max:150'],
            'trade_name' => ['nullable', 'string', 'max:150'],
            'rccm' => ['required', 'string', 'max:50', Rule::unique('companies', 'rccm')->ignore($company)],
            'nif' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150', Rule::unique('companies', 'email')->ignore($company)],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', Rule::in(array_keys(config('geography')))],
            'city' => ['nullable', 'required_with:province', 'string', 'max:100', new LocalityInProvince],
            'transport_mode_id' => [
                'required',
                'integer',
                Rule::exists('transport_modes', 'id')->where(fn ($query) => $query->where('label', '!=', 'air')),
            ],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $modeId = $this->integer('transport_mode_id');
            if ($modeId && $this->route('company')->fleetUnits()->whereNotNull('transport_mode_id')->where('transport_mode_id', '!=', $modeId)->exists()) {
                $validator->errors()->add('transport_mode_id', 'Le mode de transport ne peut pas être changé tant que la flotte contient des unités d’un autre mode.');
            }
        }];
    }

    public function messages(): array
    {
        return (new StoreCompanyRequest)->messages();
    }
}
