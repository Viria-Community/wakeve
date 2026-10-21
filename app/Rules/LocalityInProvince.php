<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class LocalityInProvince implements DataAwareRule, ValidationRule
{
    private array $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public static function provinceFor(string $city): ?string
    {
        $normalized = Str::lower(Str::ascii(trim($city)));
        foreach (config('geography') as $code => $province) {
            foreach ($province['cities'] as $known) {
                if (Str::lower(Str::ascii($known)) === $normalized) {
                    return $code;
                }
            }
        }

        return null;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || empty($this->data['province'])) {
            return;
        }
        $known = self::provinceFor($value);
        if ($known && $known !== $this->data['province']) {
            $fail('Cette ville appartient à la province '.config('geography.'.$known.'.name').'. Corrigez la province ou la ville.');
        }
    }
}
