<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeCompanyStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user?->can('changeStatus', $this->route('company'))) {
            return false;
        }

        return match ($this->input('action')) {
            'validate', 'reject' => $user->permitsPlatform('companies.validate'),
            'suspend', 'reactivate' => $user->permitsPlatform('companies.suspend'),
            default => false,
        };
    }

    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['validate', 'reject', 'suspend', 'reactivate'])],
            'reason' => [
                Rule::requiredIf(fn () => in_array($this->input('action'), ['reject', 'suspend'], true)),
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'action' => 'décision',
            'reason' => 'motif',
        ];
    }
}
