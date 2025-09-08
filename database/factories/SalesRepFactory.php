<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SalesRep>
 */
class SalesRepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->randomNumber(),
            'external_last_modified_at' => $this->faker->dateTime(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail()
        ];
    }
}
