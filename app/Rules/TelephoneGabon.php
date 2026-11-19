<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneGabon implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $numero = preg_replace('/[\s.\-]/', '', (string) $value);

        if (! preg_match('/^(\+241|00241)?[0-9]{8}$/', $numero)) {
            $fail('Le numéro de téléphone doit être un numéro gabonais à 8 chiffres, avec ou sans l\'indicatif +241.');
        }
    }
}
