<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = [
            [
                'company' => 'Enterprise MarTech Studio',
                'position' => 'Senior Full-Stack Engineer',
                'location' => 'Remote',
                'employment_type' => 'Full-time',
                'started_at' => '2024-05-01',
                'ended_at' => null,
                'is_current' => true,
                'description' => 'Technical lead on B2B marketing platforms for enterprise clients: partner portals, '
                    .'attribution dashboards and the integration layer between product data and the CRM stack.',
                'highlights' => [
                    'Designed a queue-backed sync service (NestJS + RabbitMQ) moving millions of lead and activity records a month between Salesforce, Marketo and internal systems.',
                    'Rebuilt a reporting dashboard on pre-aggregated MySQL rollups, cutting median page load from ~9s to under 400ms.',
                    'Introduced contract tests around every external API client, turning silent integration failures into caught regressions.',
                    'Mentored three developers and set the code review and release standards for the team.',
                ],
                'stack' => ['Laravel', 'NestJS', 'Vue 3', 'MySQL', 'MongoDB', 'RabbitMQ', 'Salesforce', 'Marketo'],
            ],
            [
                'company' => 'B2B Marketing Agency',
                'position' => 'Full-Stack Developer',
                'location' => 'Remote',
                'employment_type' => 'Full-time',
                'started_at' => '2022-03-01',
                'ended_at' => '2024-04-30',
                'is_current' => false,
                'description' => 'Built client-facing portals and campaign tooling for B2B marketing teams, owning '
                    .'features end to end from data model to interface.',
                'highlights' => [
                    'Delivered a white-label client reporting portal (Laravel + Vue 2) used by 30+ accounts.',
                    'Automated HubSpot lead routing and enrichment, removing a daily manual hand-off between sales and marketing ops.',
                    'Consolidated a fleet of standalone WordPress sites into one multi-site marketing platform with shared components.',
                    'Cut a legacy report generator from 40 minutes to under 2 by moving it to chunked queue jobs.',
                ],
                'stack' => ['Laravel', 'Vue 2', 'WordPress', 'MySQL', 'HubSpot', 'Express'],
            ],
            [
                'company' => 'Digital Production Agency',
                'position' => 'Web Developer',
                'location' => 'Remote',
                'employment_type' => 'Full-time',
                'started_at' => '2020-08-01',
                'ended_at' => '2022-02-28',
                'is_current' => false,
                'description' => 'Delivered corporate sites, landing pages and internal tools across a legacy PHP '
                    .'stack — the work where I learned how enterprise codebases actually age.',
                'highlights' => [
                    'Maintained and extended Yii 1 and Yii 2 applications, including a client portal serving daily operations.',
                    'Shipped 20+ production sites on WordPress and custom PHP with hand-written HTML/CSS/JS front ends.',
                    'Took over an unmaintained legacy project, documented it and stabilised its release process.',
                ],
                'stack' => ['PHP', 'Yii 1', 'Yii 2', 'WordPress', 'JavaScript', 'MySQL'],
            ],
        ];

        foreach ($experiences as $index => $experience) {
            Experience::updateOrCreate(
                ['company' => $experience['company'], 'position' => $experience['position']],
                $experience + ['sort_order' => $index * 10]
            );
        }
    }
}
