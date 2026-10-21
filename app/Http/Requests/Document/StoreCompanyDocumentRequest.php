<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('company')) ?? false;
    }

    public function rules(): array
    {
        return [
            'document_type_id' => [
                'required',
                Rule::exists('document_types', 'id')->whereIn('applicable_to', ['company', 'both']),
                Rule::unique('company_documents', 'document_type_id')
                    ->where('company_id', $this->route('company')->id),
            ],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
            'issued_at' => ['nullable', 'date', 'before_or_equal:today'],
            'valid_until' => ['required', 'date', 'after_or_equal:issued_at'],
        ];
    }

    public function attributes(): array
    {
        return ['document_type_id' => 'type de document'];
    }
}
