<?php

namespace Database\Factories;

use App\Models\Departure;
use App\Models\Line;
use App\Models\FleetUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartureFactory extends Factory
{
    public function definition(): array
    {
        return [
            'departure_date' => $this->faker->date(),
            'departure_time' => $this->faker->time(),
            'status' => Departure::STATUS_SCHEDULED,
            'line_id' => null,
            'fleet_unit_id' => null,
            'agency_id' => null,
        ];
    }
}
