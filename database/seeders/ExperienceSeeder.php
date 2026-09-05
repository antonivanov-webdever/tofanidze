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
                'company' => 'Netwrix Georgia',
                'position' => 'Senior Full-Stack Engineer',
                'location' => 'Georgia',
                'employment_type' => 'Full-time',
                'started_at' => '2024-01-01',
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
                'stack' => ['Laravel', 'NestJS', 'Sanity', 'Netlify', 'Vue 3', 'MySQL', 'MongoDB', 'RabbitMQ', 'Salesforce', 'Marketo'],
            ],
            [
                'company' => 'Horizons',
                'position' => 'Full-Stack Developer',
                'location' => 'Georgia',
                'employment_type' => 'Full-time',
                'started_at' => '2022-05-01',
                'ended_at' => '2023-12-31',
                'is_current' => false,
                'description' => 'Built client-facing portals and campaign tooling for B2B marketing teams, owning '
                    .'features end to end from data model to interface.',
                'highlights' => [
                    'Delivered a white-label client reporting portal (Laravel + Vue 2) used by 30+ accounts.',
                    'Automated HubSpot lead routing and enrichment, removing a daily manual hand-off between sales and marketing ops.',
                    'Consolidated a fleet of standalone WordPress sites into one multi-site marketing platform with shared components.',
                    'Cut a legacy report generator from 40 minutes to under 2 by moving it to chunked queue jobs.',
                ],
                'stack' => ['Yii 1', 'Vue 2', 'WordPress', 'MySQL', 'HubSpot', 'NestJS'],
            ],
            [
                'company' => 'Netwrix-Europe',
                'position' => 'Web Developer',
                'location' => 'Russia',
                'employment_type' => 'Full-time',
                'started_at' => '2021-12-01',
                'ended_at' => '2022-04-30',
                'is_current' => false,
                'description' => 'Maintaned corporate site, the Customer, Support and Partner portals. Created 5 CMS',
                'highlights' => [
                    'Maintained and extended Yii 1 and Yii 2 applications, including a client portal serving daily operations.',
                ],
                'stack' => ['PHP', 'Yii 1', 'Yii 2', 'WordPress', 'JavaScript', 'Vue 2', 'MySQL'],
            ],
            [
                'company' => 'Mockup.Digital',
                'position' => 'Web Developer',
                'location' => 'Russia',
                'employment_type' => 'Full-time',
                'started_at' => '2020-06-01',
                'ended_at' => '2021-11-30',
                'is_current' => false,
                'description' => 'Delivered corporate sites, landing pages and internal tools across a legacy PHP '
                    .'stack — the work where I learned how enterprise codebases actually age.',
                'highlights' => [
                    'Shipped 20+ production sites on WordPress and custom PHP with hand-written HTML/CSS/JS front ends.',
                    'Took over an unmaintained legacy project, documented it and stabilised its release process.',
                ],
                'stack' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'WordPress', 'MySQL'],
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
