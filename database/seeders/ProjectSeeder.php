<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Partner Portal for an Enterprise SaaS Vendor',
                'client' => 'Enterprise SaaS vendor',
                'industry' => 'B2B SaaS',
                'role' => 'Lead full-stack engineer',
                'year' => 2025,
                'duration' => '7 months',
                'team_size' => '4 engineers, 1 designer',
                'is_featured' => true,
                'summary' => 'A multi-tenant portal where 600+ resellers register deals, track commission and pull '
                    .'co-marketing assets — with every deal mirrored into Salesforce in near real time.',
                'challenge' => 'Partner deal registration lived in a spreadsheet mailed around once a week. Sales had no '
                    .'reliable view of the partner pipeline, commission disputes took days to resolve, and every new '
                    .'partner tier meant another manual process. Whatever replaced it had to reconcile with Salesforce '
                    .'as the system of record without letting either side overwrite the other.',
                'solution' => 'I designed a multi-tenant Laravel application with role-based access down to the field '
                    .'level: partner admins, partner reps, channel managers and finance each see a different slice of '
                    .'the same deal. The Vue 3 front end handles the dense parts — deal pipeline, commission '
                    ."statements, asset library — while the server owns every state transition.\n\n"
                    .'Salesforce sync runs through a RabbitMQ queue with idempotent handlers keyed on external IDs, so '
                    .'a retry never creates a duplicate opportunity. Conflicts resolve by field-level ownership rules '
                    .'rather than last-write-wins, and everything that touches a deal lands in an append-only audit log '
                    .'that finance can read without asking an engineer.',
                'outcome' => 'Deal registration moved entirely into the portal within two quarters. Commission disputes '
                    .'dropped because every number traces back to an auditable event, and onboarding a new partner tier '
                    .'became a configuration change instead of a release.',
                'metrics' => [
                    ['value' => '600+', 'label' => 'active partner accounts'],
                    ['value' => '<2 min', 'label' => 'deal-to-Salesforce latency'],
                    ['value' => '0', 'label' => 'duplicate opportunities after launch'],
                ],
                'highlights' => [
                    'Field-level RBAC across four partner roles',
                    'Idempotent, queue-backed Salesforce sync with dead-letter replay',
                    'Append-only audit trail for every commission-relevant event',
                ],
                'technologies' => ['Laravel', 'PHP', 'Vue 3', 'MySQL', 'RabbitMQ', 'Salesforce', 'REST APIs'],
            ],
            [
                'title' => 'Multi-Touch Attribution Dashboard',
                'client' => 'Global manufacturing group',
                'industry' => 'Manufacturing',
                'role' => 'Full-stack engineer',
                'year' => 2025,
                'duration' => '5 months',
                'team_size' => '3 engineers',
                'is_featured' => true,
                'summary' => 'Campaign reporting across Marketo, Salesforce and web analytics — 40M+ touchpoints '
                    .'rendered as funnel, cohort and attribution views that load in under a second.',
                'challenge' => 'Marketing ran on three sources of truth that disagreed: Marketo said one thing about '
                    .'campaign performance, Salesforce another, and the web analytics export a third. Leadership had '
                    .'stopped trusting all of them. The previous dashboard queried raw event tables directly and took '
                    .'nine seconds to load a single quarter.',
                'solution' => 'I built a nightly and incremental ETL that normalises touchpoints from all three sources '
                    .'into one event model with a stable identity graph — resolving the same human across an anonymous '
                    ."web session, a Marketo lead and a Salesforce contact.\n\n"
                    .'Reporting reads from pre-aggregated rollup tables rebuilt by queue workers rather than from raw '
                    .'events, so query cost stays flat as history grows. On top sits a Vue 3 dashboard with first-touch, '
                    .'last-touch and linear attribution models the user can switch between, plus scheduled PDF and CSV '
                    .'exports for the monthly board pack.',
                'outcome' => 'One agreed set of numbers across marketing and sales. The monthly reporting cycle went '
                    .'from a multi-day manual reconciliation to a scheduled export.',
                'metrics' => [
                    ['value' => '40M+', 'label' => 'touchpoints modelled'],
                    ['value' => '9s → 380ms', 'label' => 'median dashboard load'],
                    ['value' => '3 → 1', 'label' => 'sources of truth'],
                ],
                'highlights' => [
                    'Identity resolution across anonymous, Marketo and Salesforce records',
                    'Rollup tables that keep query cost flat as history grows',
                    'Switchable first-touch, last-touch and linear attribution models',
                ],
                'technologies' => ['Laravel', 'PHP', 'Vue 3', 'MySQL', 'Marketo', 'Salesforce'],
            ],
            [
                'title' => 'Salesforce ↔ Marketo Sync Service',
                'client' => 'Enterprise MarTech client',
                'industry' => 'B2B Software',
                'role' => 'Backend engineer',
                'year' => 2024,
                'duration' => '4 months',
                'team_size' => '2 engineers',
                'is_featured' => true,
                'summary' => 'A NestJS microservice keeping leads, contacts and activities consistent between '
                    .'Salesforce and Marketo under strict API rate limits — with replayable failures.',
                'challenge' => 'The existing sync was a cron script that fetched everything, pushed everything, and '
                    .'failed silently. When it hit a Salesforce API limit it dropped the batch; nobody found out until '
                    .'a sales rep noticed a missing lead days later. Rebuilding it meant handling two APIs with '
                    .'different rate-limit models, different field semantics and no shared identifier.',
                'solution' => 'I built a standalone NestJS service around a RabbitMQ topology: one queue per direction, '
                    .'a delay queue for rate-limited retries with exponential backoff, and a dead-letter queue that '
                    ."keeps the original payload so any failure can be inspected and replayed by hand.\n\n"
                    .'Every operation is idempotent on an external-ID key, so replays are safe. Field mapping and '
                    .'transformation rules live in configuration rather than code, which means marketing ops can add a '
                    .'custom field without a deployment. MongoDB stores sync state and a full payload history for '
                    .'debugging, and a small status endpoint surfaces queue depth and last-success timestamps to '
                    .'monitoring.',
                'outcome' => 'Silent data loss stopped being a category of incident: failures now surface within '
                    .'minutes and every one of them is replayable from stored payloads.',
                'metrics' => [
                    ['value' => '2M+', 'label' => 'records synced / month'],
                    ['value' => '99.9%', 'label' => 'sync success rate'],
                    ['value' => '100%', 'label' => 'failures replayable'],
                ],
                'highlights' => [
                    'RabbitMQ retry, delay and dead-letter topology',
                    'Config-driven field mapping — no deploy for a new custom field',
                    'Full payload history in MongoDB for audit and replay',
                ],
                'technologies' => ['NestJS', 'Node.js', 'MongoDB', 'RabbitMQ', 'Salesforce', 'Marketo', 'REST APIs'],
            ],
            [
                'title' => 'HubSpot Lead Routing & Enrichment Engine',
                'client' => 'B2B services company',
                'industry' => 'Professional Services',
                'role' => 'Full-stack engineer',
                'year' => 2023,
                'duration' => '3 months',
                'team_size' => 'Solo',
                'is_featured' => true,
                'summary' => 'Rules-driven lead scoring, enrichment and territory routing on top of HubSpot, replacing '
                    .'a manual triage that ran once a day.',
                'challenge' => 'Inbound leads sat in a shared inbox until someone triaged them by hand — typically the '
                    ."next morning, sometimes Monday. Response time was the company's main competitive weakness, and "
                    ."territory assignment rules lived in one operations manager's head.",
                'solution' => 'A Laravel service consumes HubSpot webhooks, enriches each lead against company data, '
                    .'scores it against configurable rules and assigns an owner by territory and round-robin load '
                    ."balancing — writing the result straight back to HubSpot.\n\n"
                    .'The routing rules are editable in an admin UI, so operations changes territories without '
                    .'engineering. Everything runs through queued jobs with retry, and an audit view shows exactly why '
                    .'any given lead went to any given rep — which turned out to matter more than the routing itself '
                    .'when reps disputed assignments.',
                'outcome' => 'Median first response fell from roughly 14 hours to under 10 minutes, and territory rules '
                    .'became documented configuration instead of tribal knowledge.',
                'metrics' => [
                    ['value' => '14h → 10min', 'label' => 'median first response'],
                    ['value' => '100%', 'label' => 'leads auto-routed'],
                    ['value' => '0', 'label' => 'manual triage steps'],
                ],
                'highlights' => [
                    'Webhook-driven, queue-backed processing with retries',
                    'Admin-editable scoring and territory rules',
                    'Per-lead routing audit trail',
                ],
                'technologies' => ['Laravel', 'PHP', 'MySQL', 'HubSpot', 'Vue 3', 'REST APIs'],
            ],
            [
                'title' => 'Legacy Yii 1 Portal Modernisation',
                'client' => 'Logistics operator',
                'industry' => 'Logistics',
                'role' => 'Lead engineer',
                'year' => 2023,
                'duration' => '9 months',
                'team_size' => '3 engineers',
                'is_featured' => false,
                'summary' => 'Migrating a business-critical Yii 1 client portal to Laravel in slices, without a freeze '
                    .'and without a big-bang cutover.',
                'challenge' => 'A ten-year-old Yii 1 portal ran daily operations for hundreds of B2B clients. It sat on '
                    .'an unsupported framework and an EOL PHP version, had no test coverage, and business logic lived '
                    .'inside controllers and view templates. A rewrite-and-switch was not an option — the portal could '
                    .'not stop working for a single day.',
                'solution' => 'I used a strangler-fig approach: a routing layer in front of both applications sent '
                    .'migrated paths to the new Laravel app and everything else to the legacy one, with a shared '
                    ."session so users never saw a second login.\n\n"
                    .'Module by module, I extracted business logic out of legacy controllers into tested service '
                    .'classes, starting with the highest-traffic screens. Each slice shipped to production behind a '
                    ."feature flag with the old path still available as a fallback. Once a module's traffic had fully "
                    .'moved, the legacy code was deleted.',
                'outcome' => 'The portal reached a supported PHP and framework version with no downtime and no feature '
                    .'freeze, and arrived with test coverage on the paths that matter.',
                'metrics' => [
                    ['value' => '0', 'label' => 'hours of downtime'],
                    ['value' => '9 mo', 'label' => 'incremental migration'],
                    ['value' => '70%+', 'label' => 'coverage on critical paths'],
                ],
                'highlights' => [
                    'Strangler-fig routing with a shared session across both apps',
                    'Feature-flagged, reversible cutover per module',
                    'Business logic extracted into tested services',
                ],
                'technologies' => ['Yii 1', 'Yii 2', 'Laravel', 'PHP', 'MySQL', 'JavaScript'],
            ],
            [
                'title' => 'Multi-Brand WordPress Marketing Platform',
                'client' => 'Marketing group',
                'industry' => 'Marketing',
                'role' => 'Full-stack engineer',
                'year' => 2022,
                'duration' => '4 months',
                'team_size' => '2 engineers',
                'is_featured' => false,
                'summary' => 'Twelve standalone brand sites consolidated into one multi-site platform with shared '
                    .'components, a common design system and centralised lead capture.',
                'challenge' => 'Twelve WordPress installs, twelve themes, twelve plugin sets and twelve update schedules. '
                    .'A change to the lead form meant twelve deployments, and lead data arrived in the CRM in twelve '
                    .'slightly different shapes.',
                'solution' => 'I consolidated everything onto a WordPress multi-site with one shared parent theme and '
                    .'per-brand child themes carrying only the visual differences. Reusable Gutenberg blocks replaced '
                    ."duplicated page templates.\n\n"
                    .'Lead capture moved behind a single normalising endpoint that validates, de-duplicates and forwards '
                    .'to the CRM in one consistent schema, so a form change ships once for all brands.',
                'outcome' => 'One deployment instead of twelve, a consistent lead schema in the CRM, and new brand '
                    .'launches measured in days rather than weeks.',
                'metrics' => [
                    ['value' => '12 → 1', 'label' => 'sites to maintain'],
                    ['value' => '1 schema', 'label' => 'for all lead capture'],
                    ['value' => '~80%', 'label' => 'less duplicated template code'],
                ],
                'highlights' => [
                    'Shared parent theme with per-brand child themes',
                    'Reusable block library instead of duplicated templates',
                    'Single normalising lead-capture endpoint',
                ],
                'technologies' => ['WordPress', 'PHP', 'MySQL', 'JavaScript', 'HTML', 'CSS'],
            ],
            [
                'title' => 'Headless Content Hub on Sanity',
                'client' => 'B2B SaaS scale-up',
                'industry' => 'B2B SaaS',
                'role' => 'Full-stack engineer',
                'year' => 2024,
                'duration' => '3 months',
                'team_size' => '2 engineers',
                'is_featured' => false,
                'summary' => 'A headless content platform feeding a marketing site, an in-product resource centre and '
                    .'email campaigns from one editorial source.',
                'challenge' => 'The same case study existed in three places — the marketing site CMS, the in-product '
                    .'help centre and the email tool — and they drifted apart within weeks of any edit. Editors had no '
                    .'preview and published by asking a developer to deploy.',
                'solution' => 'Sanity became the single editorial source, with a content model built around reusable '
                    .'blocks rather than page-shaped documents. An Express API layer handles caching, on-demand '
                    ."revalidation and delivery to each consumer in the shape it needs.\n\n"
                    .'The React front end renders those blocks with live preview, so editors see the result before '
                    .'publishing and ship without a deployment.',
                'outcome' => 'Content stopped drifting between channels, and publishing became an editorial action '
                    .'rather than an engineering ticket.',
                'metrics' => [
                    ['value' => '3 → 1', 'label' => 'content sources'],
                    ['value' => 'instant', 'label' => 'publish without deploy'],
                    ['value' => '3', 'label' => 'channels from one model'],
                ],
                'highlights' => [
                    'Block-based content model instead of page-shaped documents',
                    'Express caching and on-demand revalidation layer',
                    'Live preview for editors',
                ],
                'technologies' => ['Sanity', 'Node.js', 'Express', 'React', 'JavaScript'],
            ],
        ];

        foreach ($projects as $index => $data) {
            $technologies = $data['technologies'];
            unset($data['technologies']);

            $project = Project::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data + [
                    'is_published' => true,
                    'sort_order' => $index * 10,
                    'meta_description' => Str::limit($data['summary'], 155),
                ]
            );

            $project->technologies()->sync(
                Technology::whereIn('name', $technologies)->pluck('id')
            );
        }
    }
}
