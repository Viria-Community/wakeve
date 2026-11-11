<?php

namespace App\Http\Requests\Line;

use App\Models\Line;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLineStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('line')) ?? false;
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
