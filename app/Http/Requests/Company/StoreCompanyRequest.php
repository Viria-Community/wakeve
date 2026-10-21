<?php

namespace App\Http\Requests\Company;

use App\Models\Company;
use App\Rules\LocalityInProvince;
use App\Support\GeographyInput;
use App\Support\RccmInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        GeographyInput::normalize($this);
        RccmInput::normalize($this);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', Company::class) ?? false;
    }

    public function rules(): array
    {
        return $this->companyRules();
    }

    protected function companyRules(): array
    {
        return [
            'legal_name' => ['required', 'string', 'max:150'],
            'trade_name' => ['nullable', 'string', 'max:150'],
            'rccm' => ['required', 'string', 'max:50', 'unique:companies,rccm'],
            'nif' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150', 'unique:companies,email'],
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

    public function messages(): array
    {
        return [
            'legal_name.required' => 'La raison sociale est obligatoire.',
            'rccm.required' => 'Le numéro RCCM est obligatoire.',
            'rccm.unique' => 'Ce numéro RCCM est déjà enregistré par une compagnie.',
            'email.required' => 'L’adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée par une compagnie.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'transport_mode_id.required' => 'Sélectionnez un mode de transport.',
            'transport_mode_id.exists' => 'Sélectionnez un mode de transport pris en charge par la plateforme.',
        ];
    }
}
