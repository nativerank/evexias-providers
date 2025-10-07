<?php

namespace Database\Factories;

use App\LeadSource;
use App\LeadStatus;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'practice_id' => Practice::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'data' => [
                'message' => fake()->paragraph(),
                'interests' => fake()->randomElements(['weight loss', 'hormone therapy', 'wellness', 'anti-aging'], 2),
            ],
            'source' => fake()->randomElement(LeadSource::cases()),
            'status' => LeadStatus::NEW,
            'lead_created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'contacted_at' => null,
        ];
    }

    public function contacted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::CONTACTED,
            'contacted_at' => fake()->dateTimeBetween($attributes['lead_created_at'], 'now'),
        ]);
    }

    public function qualified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::QUALIFIED,
            'contacted_at' => fake()->dateTimeBetween($attributes['lead_created_at'], 'now'),
        ]);
    }

    public function converted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeadStatus::CONVERTED,
            'contacted_at' => fake()->dateTimeBetween($attributes['lead_created_at'], 'now'),
        ]);
    }
}
