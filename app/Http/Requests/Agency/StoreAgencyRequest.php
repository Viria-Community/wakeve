<?php

namespace App\Http\Requests\Agency;

use App\Models\Agency;
use App\Rules\LocalityInProvince;
use App\Support\GeographyInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAgencyRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        GeographyInput::normalize($this);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', Agency::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => [$this->user()->isAdmin() ? 'required' : 'nullable', 'integer', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', Rule::in(array_keys(config('geography')))],
            'city' => ['nullable', 'required_with:province', 'string', 'max:100', new LocalityInProvince],
            'phone' => ['nullable', 'string', 'max:30', new \App\Rules\TelephoneGabon],
        ];
    }

    public function messages(): array
    {
        return [
            'company_id.required' => 'La compagnie de rattachement est obligatoire.',
            'name.required' => 'Le nom de l’agence est obligatoire.',
        ];
    }
}
