<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::headline(fake()->unique()->word());

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(array_keys(Technology::CATEGORIES)),
            'color' => fake()->hexColor(),
            'sort_order' => 0,
            'is_featured' => false,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
