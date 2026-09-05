<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            // [name, category, color, featured]
            ['PHP', 'backend', '#777BB4', true],
            ['Laravel', 'backend', '#FF2D20', true],
            ['Yii 2', 'backend', '#4098D3', true],
            ['Yii 1', 'backend', '#4098D3', false],
            ['WordPress', 'backend', '#21759B', false],
            ['Node.js', 'backend', '#5FA04E', true],
            ['NestJS', 'backend', '#E0234E', true],
            ['Express', 'backend', '#68A063', false],
            ['Sanity', 'backend', '#F03E2F', false],
            ['REST APIs', 'backend', '#38BDF8', false],

            ['Vue 3', 'frontend', '#42B883', true],
            ['Vue 2', 'frontend', '#42B883', false],
            ['React', 'frontend', '#61DAFB', true],
            ['JavaScript', 'frontend', '#F7DF1E', true],
            ['HTML', 'frontend', '#E34F26', false],
            ['CSS', 'frontend', '#1572B6', false],

            ['MySQL', 'database', '#00758F', true],
            ['MongoDB', 'database', '#47A248', true],
            ['RabbitMQ', 'database', '#FF6600', true],

            ['Salesforce', 'integration', '#00A1E0', true],
            ['Marketo', 'integration', '#5C4C9F', true],
            ['HubSpot', 'integration', '#FF7A59', true],
        ];

        foreach ($technologies as $index => [$name, $category, $color, $featured]) {
            Technology::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'category' => $category,
                    'color' => $color,
                    'is_featured' => $featured,
                    'sort_order' => $index * 10,
                ]
            );
        }
    }
}
