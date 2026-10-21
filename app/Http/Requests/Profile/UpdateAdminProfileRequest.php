<?php

namespace App\Http\Requests\Profile;

use App\Support\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user())],
            'current_password' => ['required_with:password', 'nullable', 'current_password'],
            'password' => ['nullable', 'confirmed', StrongPassword::rule()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L’adresse email est obligatoire.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'current_password.required_with' => 'Le mot de passe actuel est obligatoire pour définir un nouveau mot de passe.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ];
    }
}
