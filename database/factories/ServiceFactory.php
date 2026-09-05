<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(2, true),
            'subtitle' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'icon' => 'layers',
            'bullets' => [fake()->sentence(6), fake()->sentence(6)],
            'sort_order' => 0,
            'is_published' => true,
        ];
    }
}
