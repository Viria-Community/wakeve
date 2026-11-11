<?php

namespace App\Http\Requests\Fare;

use App\Models\Fare;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFareStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('fare')) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être actif ou inactif.',
        ];
    }
}
