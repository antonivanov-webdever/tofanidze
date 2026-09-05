<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Enterprise portals',
                'subtitle' => 'Partner, client and internal platforms',
                'description' => 'Role-based portals where partners, sales teams and clients work with the same data '
                    .'without stepping on each other: granular permissions, audit trails, bulk operations and an '
                    .'admin layer your team can actually operate.',
                'icon' => 'building',
                'bullets' => [
                    'Multi-tenant architecture and role-based access control',
                    'Document workflows, approvals and audit logging',
                    'SSO and directory integration',
                ],
            ],
            [
                'title' => 'Marketing dashboards',
                'subtitle' => 'Campaign, funnel and attribution reporting',
                'description' => 'Dashboards that answer the questions marketing leadership actually asks — where the '
                    .'pipeline came from, which campaign paid for itself, and why last month looks different from '
                    .'the CRM report.',
                'icon' => 'chart',
                'bullets' => [
                    'Funnel, cohort and multi-touch attribution views',
                    'Pre-aggregated metrics that stay fast at millions of rows',
                    'Scheduled exports and white-label client reporting',
                ],
            ],
            [
                'title' => 'MarTech integrations',
                'subtitle' => 'Salesforce · Marketo · HubSpot',
                'description' => 'Two-way syncs between your product, your CRM and your marketing automation platform. '
                    .'Queue-backed, idempotent and observable — so a rate limit or a bad payload never silently '
                    .'drops a lead.',
                'icon' => 'plug',
                'bullets' => [
                    'Bi-directional lead, contact and account sync',
                    'Field mapping, deduplication and conflict resolution',
                    'Retry, replay and dead-letter handling on RabbitMQ',
                ],
            ],
            [
                'title' => 'Legacy modernisation',
                'subtitle' => 'Yii 1/2 and WordPress rescue work',
                'description' => 'Inherited a codebase nobody wants to touch? I stabilise it first, cover the critical '
                    .'paths with tests, then migrate it in slices that ship — no eighteen-month rewrite with no '
                    .'business value in between.',
                'icon' => 'refresh',
                'bullets' => [
                    'Incremental migration with the strangler-fig pattern',
                    'Performance and N+1 query audits',
                    'API extraction from monolithic templates',
                ],
            ],
        ];

        foreach ($services as $index => $service) {
            Service::updateOrCreate(
                ['title' => $service['title']],
                $service + ['sort_order' => $index * 10, 'is_published' => true]
            );
        }
    }
}
