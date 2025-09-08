<?php

namespace Database\Factories;

use App\PracticeStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Practice>
 */
class PracticeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'lng' => $this->faker->longitude(),
            'lat' => $this->faker->latitude(),
            'external_id' => $this->faker->randomNumber(),
            'phone' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(PracticeStatus::cases()),
        ];
    }
}
