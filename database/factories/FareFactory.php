<?php

namespace Database\Factories;

use App\Models\Fare;
use Illuminate\Database\Eloquent\Factories\Factory;

class FareFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(2, 1, 100),
            'fare_class' => $this->faker->randomElement(['Standard', 'Premium', 'Business']),
            'status' => Fare::STATUS_ACTIVE,
        ];
    }
}
