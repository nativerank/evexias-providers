<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Practitioner>
 */
class PractitionerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'external_id' => $this->faker->unique()->randomNumber(),
            'external_last_modified_at' => $this->faker->dateTime(),
            'active' => $this->faker->boolean(80),
            'email' => $this->faker->unique()->safeEmail(),
            'specialization' => $this->faker->word(),
            'type' => $this->faker->randomElement(\App\PractitionerType::cases()),
        ];
    }
}
