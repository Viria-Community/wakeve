<?php

namespace App\Http\Requests\Agency;

use App\Rules\LocalityInProvince;
use App\Support\GeographyInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgencyRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        GeographyInput::normalize($this);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('agency')) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', Rule::in(array_keys(config('geography')))],
            'city' => ['nullable', 'required_with:province', 'string', 'max:100', new LocalityInProvince],
            'phone' => ['nullable', 'string', 'max:30', new \App\Rules\TelephoneGabon],
            'status' => ['required', Rule::in($this->route('agency')?->headquarters_for ? ['active'] : ['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l’agence est obligatoire.',
            'status.in' => 'Le statut de l’agence est invalide.',
        ];
    }
}
