<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Backend',
                'description' => 'Domain modelling, APIs, background processing and the glue that holds enterprise systems together.',
                'icon' => 'server',
                'skills' => [
                    ['PHP 8', 95, 6],
                    ['Laravel', 95, 5],
                    ['Yii 2', 85, 4],
                    ['Yii 1', 70, 3],
                    ['NestJS', 85, 3],
                    ['Express', 80, 4],
                    ['WordPress', 75, 5],
                    ['REST API design', 90, 6],
                ],
            ],
            [
                'name' => 'Frontend',
                'description' => 'Dashboards and portals that stay fast when the data set stops being small.',
                'icon' => 'layout',
                'skills' => [
                    ['Vue 3 / Composition API', 90, 4],
                    ['Vue 2 / Options API', 85, 5],
                    ['React', 80, 4],
                    ['JavaScript (ES2023)', 90, 6],
                    ['HTML / CSS', 90, 6],
                ],
            ],
            [
                'name' => 'Data & Messaging',
                'description' => 'Schema design, query tuning and asynchronous pipelines between systems.',
                'icon' => 'database',
                'skills' => [
                    ['MySQL', 90, 6],
                    ['MongoDB', 80, 4],
                    ['RabbitMQ', 85, 4],
                    ['Query optimisation', 85, 5],
                ],
            ],
            [
                'name' => 'MarTech Integrations',
                'description' => 'Two-way syncs, lead routing and attribution across the marketing stack.',
                'icon' => 'plug',
                'skills' => [
                    ['Salesforce API', 90, 4],
                    ['Marketo REST / SOAP', 85, 4],
                    ['HubSpot API', 85, 3],
                    ['Webhooks & event pipelines', 90, 5],
                ],
            ],
        ];

        foreach ($categories as $categoryIndex => $data) {
            $category = SkillCategory::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'sort_order' => $categoryIndex * 10,
                ]
            );

            foreach ($data['skills'] as $skillIndex => [$name, $level, $years]) {
                Skill::updateOrCreate(
                    ['skill_category_id' => $category->id, 'name' => $name],
                    [
                        'level' => $level,
                        'years' => $years,
                        'sort_order' => $skillIndex * 10,
                    ]
                );
            }
        }
    }
}
