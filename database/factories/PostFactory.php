<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = Str::headline(fake()->unique()->words(5, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(16),
            'body' => '## '.fake()->sentence(4)."\n\n".fake()->paragraphs(4, true),
            'tags' => ['Architecture', 'Laravel'],
            'views' => 0,
            'is_published' => true,
            'published_at' => now()->subDays(fake()->numberBetween(1, 200)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false, 'published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['is_published' => true, 'published_at' => now()->addWeek()]);
    }
}
