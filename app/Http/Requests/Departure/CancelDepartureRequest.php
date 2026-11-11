<?php

namespace App\Http\Requests\Departure;

use App\Models\Departure;
use Illuminate\Foundation\Http\FormRequest;

class CancelDepartureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('departure')) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:scheduled,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être programmé ou annulé.',
        ];
    }

    public function after(): array
    {
        return [
            function (): void {
                $departure = $this->route('departure');

                if ($departure->status === 'cancelled') {
                    $this->errors()->add('status', 'Ce départ est déjà annulé.');
                }
            },
        ];
    }
}
