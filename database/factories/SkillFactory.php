<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'skill_category_id' => SkillCategory::factory(),
            'name' => fake()->unique()->word(),
            'level' => fake()->numberBetween(60, 95),
            'years' => fake()->numberBetween(1, 6),
            'sort_order' => 0,
        ];
    }
}
