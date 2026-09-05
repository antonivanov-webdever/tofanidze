<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = Str::headline(fake()->unique()->words(4, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'client' => fake()->company(),
            'industry' => fake()->randomElement(['B2B SaaS', 'Manufacturing', 'Logistics', 'Marketing']),
            'role' => 'Full-stack engineer',
            'summary' => fake()->sentence(18),
            'challenge' => fake()->paragraphs(2, true),
            'solution' => fake()->paragraphs(2, true),
            'outcome' => fake()->paragraph(),
            'metrics' => [
                ['value' => '99.9%', 'label' => 'sync success rate'],
            ],
            'highlights' => [fake()->sentence(8), fake()->sentence(8)],
            'year' => fake()->numberBetween(2020, 2026),
            'duration' => fake()->numberBetween(2, 12).' months',
            'team_size' => 'Solo',
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
