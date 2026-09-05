<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Marketo: access_token Query Param Is Out, Authorization Header Is In',
                'excerpt' => 'Marketo is retiring token-in-the-URL authentication for its REST API. If your '
                    .'integration still appends ?access_token=… to every request, here is exactly what changes '
                    .'and the one-line fix.',
                'tags' => ['Marketo', 'Integrations', 'API'],
                'published_at' => '2026-09-01 09:00:00',
                'body' => <<<'MD'
Marketo has deprecated passing `access_token` as a query string parameter on REST API calls. Going forward,
the token must be sent as a standard `Authorization: Bearer` header instead. Requests that still put the
token in the URL will start failing once the old method is switched off.

## What actually changes

Before:

```
GET /rest/v1/leads.json?access_token=abcd1234...&filterType=email HTTP/1.1
Host: 123-ABC-456.mktorest.com
```

After:

```
GET /rest/v1/leads.json?filterType=email HTTP/1.1
Host: 123-ABC-456.mktorest.com
Authorization: Bearer abcd1234...
```

Nothing about the token itself changes — you still get it from the identity endpoint the same way. The only
difference is where it travels: out of the query string and into a header.

## Why it matters beyond "requests will fail"

A token in the URL is not just a deprecated convenience, it is a quiet liability. Query strings get written
to access logs, proxy logs, browser history and CDN caches — all in plain text. Moving to a header is a real
security improvement, not just an API formality, and it is worth treating the migration as more than a
find-and-replace.

## The fix

If your client builds the query string manually:

```php
// Before
$response = Http::get("https://{$host}/rest/v1/leads.json", [
    'access_token' => $token,
    'filterType' => 'email',
    'filterValues' => $email,
]);

// After
$response = Http::withToken($token)->get("https://{$host}/rest/v1/leads.json", [
    'filterType' => 'email',
    'filterValues' => $email,
]);
```

`Http::withToken()` sets `Authorization: Bearer <token>` for you. With Guzzle directly, it is the same idea:

```php
$client->request('GET', $url, [
    'headers' => ['Authorization' => "Bearer {$token}"],
    'query' => ['filterType' => 'email', 'filterValues' => $email],
]);
```

## Where to check

A handful of places tend to still have the old pattern hiding in them:

- **Shared HTTP client base config** — if `access_token` is injected as a default query parameter for every
  request, that one change fixes every call site at once.
- **Webhook or bulk-export helpers** — bulk extract and file-based endpoints are often written separately
  from the main lead API client and get missed in a quick pass.
- **Logging and error reporting** — if you were ever logging the full request URL for debugging, check
  those logs are not the reason the token ended up in the query string in the first place.
- **Token refresh logic** — unrelated to this change, but worth confirming while you're in this code: the
  refresh call itself still uses `client_id` / `client_secret` as query params against the identity endpoint,
  that part is untouched.

## Bottom line

This is a small, mechanical change with a firm deadline attached. Swap the query parameter for a header on
every Marketo REST call, confirm nothing downstream is still stitching `access_token=` into a URL, and move
on — but do it before Marketo flips the switch on the old method, not after something starts silently
returning 401s.
MD,
            ],
            [
                'title' => 'Idempotent CRM Sync: Why Your Integration Keeps Creating Duplicates',
                'excerpt' => 'Every retry in a CRM integration is a chance to create a second copy of the same lead. '
                    .'Here is the key design that makes retries safe — and the three places teams usually get it wrong.',
                'tags' => ['Integrations', 'Salesforce', 'RabbitMQ', 'Architecture'],
                'published_at' => '2026-06-18 09:00:00',
                'body' => <<<'MD'
Every integration between a product and a CRM eventually hits the same wall: a request times out, the job retries, and
now there are two opportunities where there should be one. The network call actually succeeded — the response just
never made it back.

You cannot prevent that. What you can do is make the second attempt harmless.

## Idempotency is a key, not a flag

The mistake I see most often is treating idempotency as a boolean the queue driver provides. It is not. It is a key you
choose, and it has to be derived from the *business* identity of the record, not from the job.

```php
// Wrong: a new UUID per attempt means every retry is a new write.
$externalId = Str::uuid();

// Right: derived from the record, stable across every retry.
$externalId = "portal-deal-{$deal->id}";
```

With a stable key you stop asking "have I sent this?" and start asking the CRM "do you already have this?" — which is
the only question with a trustworthy answer. Salesforce gives you external ID fields with upsert semantics for exactly
this reason:

```php
$client->upsert('Opportunity', 'Portal_Deal_Id__c', $externalId, $payload);
```

One call, safe to repeat. No lookup-then-create race between two workers processing the same deal.

## Three places this still goes wrong

**Composite records.** A deal with line items is not one write. If the parent upserts and the child insert fails, the
retry re-upserts a parent that is already correct and re-inserts children that are already there. Give the children
their own derived keys — `portal-deal-{$deal->id}-line-{$line->id}` — not autoincrement identity.

**Rate limits treated as failures.** A 429 is not an error, it is a scheduling instruction. Retrying it immediately with
the same backoff as a real failure burns your remaining quota. Route rate-limited messages to a delay queue and let them
come back when the window resets:

```php
public function retryUntil(): DateTime
{
    return now()->addHours(6);
}

public function backoff(): array
{
    return [60, 300, 900, 3600];
}
```

**Dead letters without payloads.** A dead-letter queue that stores "job X failed" is an alert, not a recovery tool.
Store the full payload as it was at the time of the attempt. Six months in, the ability to replay a failed message
by hand is worth more than any dashboard.

## What to build first

If you are starting a CRM integration today, build these three things before the first field mapping:

1. A derived external ID on every syncable entity.
2. A delay queue for rate limits, separate from the retry path for genuine failures.
3. A dead-letter store that keeps payloads, with a one-command replay.

Everything else — field mapping, transformation rules, conflict resolution — is easier to change later. These three are
load-bearing, and retrofitting them means reconciling the duplicates you have already created.
MD,
            ],
            [
                'title' => 'Dashboards That Stay Fast: Rollup Tables Over Raw Events',
                'excerpt' => 'A marketing dashboard querying raw event tables is fine at 100k rows and unusable at 40 '
                    .'million. Pre-aggregation is the boring fix that works — here is how to structure it.',
                'tags' => ['Performance', 'MySQL', 'Laravel', 'Dashboards'],
                'published_at' => '2026-04-02 09:00:00',
                'body' => <<<'MD'
Analytics dashboards degrade in a specific way. They are fast in development, acceptable at launch, and nine seconds per
page a year later. Nothing broke — the query just started scanning a table that grew while nobody watched.

Adding indexes buys you a quarter. The structural fix is to stop querying raw events at read time.

## Separate the write model from the read model

Raw events are append-only, high-volume and shaped for writing. Dashboards want small, pre-grouped, shaped for one
specific chart. Those are different tables.

```sql
CREATE TABLE campaign_daily_rollups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    campaign_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    touchpoints INT UNSIGNED NOT NULL DEFAULT 0,
    unique_leads INT UNSIGNED NOT NULL DEFAULT 0,
    attributed_pipeline DECIMAL(14,2) NOT NULL DEFAULT 0,
    UNIQUE KEY campaign_date (campaign_id, date)
);
```

The dashboard reads this table. A year of data for one campaign is 365 rows, and the query plan does not change as the
event table grows.

## Rebuild incrementally, not nightly-from-scratch

A full nightly rebuild is simple until it takes four hours. Rebuild only the days that changed, and make the operation
safe to repeat:

```php
public function handle(): void
{
    Event::query()
        ->select('campaign_id', DB::raw('DATE(occurred_at) as date'))
        ->selectRaw('COUNT(*) as touchpoints')
        ->selectRaw('COUNT(DISTINCT lead_id) as unique_leads')
        ->selectRaw('SUM(attributed_value) as attributed_pipeline')
        ->whereBetween('occurred_at', [$this->from, $this->to])
        ->groupBy('campaign_id', 'date')
        ->chunkById(1000, function ($rows) {
            CampaignDailyRollup::upsert(
                $rows->toArray(),
                ['campaign_id', 'date'],
                ['touchpoints', 'unique_leads', 'attributed_pipeline'],
            );
        });
}
```

The unique key plus `upsert` makes the job idempotent: run it twice for the same window and the numbers do not double.
That property matters more than the speed, because it lets you re-run any window without thinking about it — after a
late-arriving batch, after a bug fix, after a backfill.

## Make staleness visible instead of hiding it

Pre-aggregated data is stale by definition. Users forgive that easily as long as you tell them; they lose trust
immediately when they find out on their own by comparing to the CRM.

Show the rollup timestamp on the dashboard — `Updated 4 minutes ago` — and give impatient users a refresh that queues a
rebuild for the visible window. In practice that single line of text ends more "is this number right?" conversations
than any amount of query tuning.

## When not to do this

If your event table is under a few million rows and growing slowly, indexes and a covering query are cheaper and simpler.
Rollups introduce a second thing to keep correct. Reach for them when read latency is a product problem, not because the
architecture diagram looks better with them.
MD,
            ],
            [
                'title' => 'Strangler-Fig in Practice: Migrating a Yii 1 Portal Without a Freeze',
                'excerpt' => 'Rewrites fail because they demand that the business stop moving. Incremental migration '
                    .'lets both applications run at once — the hard part is the session, not the routing.',
                'tags' => ['Legacy', 'Yii', 'Laravel', 'Migration'],
                'published_at' => '2026-01-22 09:00:00',
                'body' => <<<'MD'
Any proposal that begins "we rewrite it and switch over in Q3" is a proposal to stop shipping features for two quarters
and then take on all the risk in one evening. It almost never survives contact with a business that has customers.

The alternative is to run both applications simultaneously and move traffic path by path.

## Routing is the easy half

A reverse proxy in front of both applications, with an explicit list of migrated paths:

```nginx
location ~ ^/(dashboard|reports|invoices) {
    proxy_pass http://laravel_app;
}

location / {
    proxy_pass http://legacy_yii_app;
}
```

Every migrated module is one line. Rolling one back is deleting that line — which matters, because being able to revert
in seconds is what makes the team willing to migrate the risky modules at all.

## Sessions are the hard half

Users must not notice which application served the page, and that means one session across both. Two options, in order
of preference:

**Shared session store.** Point both applications at the same Redis or database session backend and teach the new one to
read the old one's serialisation format. Yii 1 uses PHP's native session serialisation; Laravel does not.

```php
// Reading a legacy Yii 1 session payload from the shared store.
public function fromLegacy(string $payload): array
{
    $original = ini_set('session.serialize_handler', 'php');
    session_decode($payload);
    ini_set('session.serialize_handler', $original);

    return $_SESSION;
}
```

**Shared cookie with a signed token.** If the session formats are too far apart, issue a short-lived signed token on a
cookie scoped to the parent domain and let each application resolve its own session from it. More moving parts, but it
survives a session driver change on either side.

Whichever you pick, write the integration test for "log in on the old app, land on a new-app page, stay logged in"
before migrating a single module. It is the failure users report loudest.

## Migrate by traffic, not by tidiness

The instinct is to start with the ugliest module. Start with the highest-traffic one instead. It gets you the most
production feedback per unit of risk, and it is the module where a performance win is visible enough to buy patience for
the rest of the migration.

For each slice: extract the business logic out of the legacy controller into a tested service class, build the new
screen against that service, ship behind a feature flag with the legacy path still reachable, then delete the legacy
code once traffic has fully moved. **Delete it in the same sprint.** A migration that leaves both implementations in
place has not reduced anything — it has doubled the surface area, and next quarter nobody will remember which one is
authoritative.
MD,
            ],
        ];

        foreach ($posts as $data) {
            Post::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                $data + [
                    'is_published' => true,
                    'meta_description' => Str::limit($data['excerpt'], 155),
                ]
            );
        }
    }
}
