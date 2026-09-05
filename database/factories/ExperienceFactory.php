<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company' => fake()->company(),
            'position' => 'Full-Stack Engineer',
            'location' => 'Remote',
            'employment_type' => 'Full-time',
            'started_at' => now()->subYears(3),
            'ended_at' => now()->subYear(),
            'is_current' => false,
            'description' => fake()->sentence(14),
            'highlights' => [fake()->sentence(10)],
            'stack' => ['Laravel', 'Vue 3'],
            'sort_order' => 0,
        ];
    }

    public function current(): static
    {
        return $this->state(fn () => ['is_current' => true, 'ended_at' => null]);
    }
}
