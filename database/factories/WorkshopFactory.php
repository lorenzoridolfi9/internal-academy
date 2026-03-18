<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $endsAt = (clone $startsAt)->modify('+2 hours');

        return [
            'created_by' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'capacity' => $this->faker->numberBetween(5, 30),
        ];
    }
}
