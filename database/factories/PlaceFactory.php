<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'place_type' => $this->faker->randomElement(['city', 'town', 'village']),
            'city' => $this->faker->city,
            'province' => $this->faker->randomElement(['G1', 'G2', 'G3', 'G4', 'G5', 'G6', 'G7', 'G8', 'G9']),
        ];
    }
}
