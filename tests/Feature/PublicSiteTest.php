<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_the_published_content(): void
    {
        $featured = Project::factory()->featured()->create(['title' => 'Partner Portal Rebuild']);
        $featured->technologies()->attach(Technology::factory()->featured()->create(['name' => 'Laravel']));

        Service::factory()->create(['title' => 'Enterprise portals']);
        Experience::factory()->current()->create(['company' => 'MarTech Studio']);
        Post::factory()->create(['title' => 'Idempotent CRM Sync']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Partner Portal Rebuild')
            ->assertSee('Enterprise portals')
            ->assertSee('MarTech Studio')
            ->assertSee('Idempotent CRM Sync');
    }

    public function test_home_page_hides_unpublished_projects(): void
    {
        Project::factory()->featured()->draft()->create(['title' => 'Secret Client Work']);

        $this->get('/')->assertOk()->assertDontSee('Secret Client Work');
    }

    public function test_projects_index_lists_published_projects_only(): void
    {
        Project::factory()->create(['title' => 'Attribution Dashboard']);
        Project::factory()->draft()->create(['title' => 'Unfinished Case Study']);

        $this->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Attribution Dashboard')
            ->assertDontSee('Unfinished Case Study');
    }

    public function test_project_page_shows_the_full_case_study(): void
    {
        $project = Project::factory()->create([
            'title' => 'Salesforce Sync Service',
            'challenge' => 'The cron script dropped batches silently.',
            'solution' => 'A queue-backed service with replayable failures.',
            'outcome' => 'Silent data loss stopped being a category of incident.',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Salesforce Sync Service')
            ->assertSee('The cron script dropped batches silently.')
            ->assertSee('A queue-backed service with replayable failures.')
            ->assertSee('Silent data loss stopped being a category of incident.');
    }

    public function test_unpublished_project_returns_404(): void
    {
        $project = Project::factory()->draft()->create();

        $this->get(route('projects.show', $project))->assertNotFound();
    }

    public function test_blog_index_hides_drafts_and_scheduled_posts(): void
    {
        Post::factory()->create(['title' => 'Published Article']);
        Post::factory()->draft()->create(['title' => 'Draft Article']);
        Post::factory()->scheduled()->create(['title' => 'Scheduled Article']);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Published Article')
            ->assertDontSee('Draft Article')
            ->assertDontSee('Scheduled Article');
    }

    public function test_blog_index_filters_by_tag(): void
    {
        Post::factory()->create(['title' => 'Queue Retries', 'tags' => ['RabbitMQ']]);
        Post::factory()->create(['title' => 'Rollup Tables', 'tags' => ['MySQL']]);

        $this->get(route('blog.index', ['tag' => 'RabbitMQ']))
            ->assertOk()
            ->assertSee('Queue Retries')
            ->assertDontSee('Rollup Tables');
    }

    public function test_post_page_renders_markdown_and_counts_a_view(): void
    {
        $post = Post::factory()->create([
            'title' => 'Strangler Fig in Practice',
            'body' => "## Routing is the easy half\n\nA reverse proxy in front of both applications.",
        ]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('<h2>Routing is the easy half</h2>', false)
            ->assertSee('A reverse proxy in front of both applications.');

        $this->assertSame(1, $post->fresh()->views);
    }

    public function test_scheduled_post_is_not_reachable(): void
    {
        $post = Post::factory()->scheduled()->create();

        $this->get(route('blog.show', $post))->assertNotFound();
    }

    public function test_about_page_renders_skills_and_experience(): void
    {
        $skill = Skill::factory()->create(['name' => 'RabbitMQ']);
        Experience::factory()->create(['company' => 'Logistics Operator']);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee($skill->category->name)
            ->assertSee('RabbitMQ')
            ->assertSee('Logistics Operator');
    }

    public function test_resume_page_and_pdf_download(): void
    {
        Experience::factory()->create(['company' => 'MarTech Studio']);

        $this->get(route('resume.show'))->assertOk()->assertSee('MarTech Studio');

        $response = $this->get(route('resume.download'));

        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_sitemap_lists_published_content_only(): void
    {
        $published = Project::factory()->create();
        $draft = Project::factory()->draft()->create();

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml')
            ->assertSee(route('projects.show', $published))
            ->assertDontSee(route('projects.show', $draft));
    }

    public function test_feed_returns_rss_for_published_posts(): void
    {
        Post::factory()->create(['title' => 'Rollup Tables Over Raw Events']);
        Post::factory()->draft()->create(['title' => 'Unfinished Thought']);

        $this->get(route('feed'))
            ->assertOk()
            ->assertHeader('content-type', 'application/xml')
            ->assertSee('Rollup Tables Over Raw Events')
            ->assertDontSee('Unfinished Thought');
    }

    public function test_robots_points_at_the_sitemap_and_blocks_admin(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee(route('sitemap'));
    }
}
