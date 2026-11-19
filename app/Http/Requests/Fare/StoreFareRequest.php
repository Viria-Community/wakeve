<?php

namespace App\Http\Requests\Fare;

use App\Models\Fare;
use App\Models\Line;
use Illuminate\Foundation\Http\FormRequest;

class StoreFareRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'line_id' => $this->route('line')?->id,
            'amount' => str_replace(',', '.', (string) $this->input('amount')),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('create', Fare::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'line_id' => ['required', 'integer', 'exists:lines,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'fare_class' => ['required', 'in:economique,standard,premiere'],
        ];
    }

    public function messages(): array
    {
        return [
            'line_id.required' => 'La ligne est obligatoire.',
            'line_id.exists' => 'La ligne sélectionnée n\'existe pas.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être strictement positif.',
            'fare_class.string' => 'La classe de tarif doit être une chaîne valide.',
            'fare_class.max' => 'La classe de tarif ne peut pas dépasser 50 caractères.',
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
            },
        ];
    }
}
