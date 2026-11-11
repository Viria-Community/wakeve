<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\TransportMode;
use Illuminate\Database\Eloquent\Factories\Factory;

class LineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'label' => $this->faker->sentence(3),
            'status' => 'active',
            'transport_mode_id' => TransportMode::factory(),
            'origin_place_id' => null,
            'destination_place_id' => null,
        ];
    }
}
