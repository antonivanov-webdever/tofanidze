<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Post;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_projects_index_returns_published_projects_only(): void
    {
        $published = Project::factory()->create(['title' => 'Published One']);
        $published->technologies()->attach(Technology::factory()->create(['name' => 'Laravel']));
        Project::factory()->draft()->create(['title' => 'Draft One']);

        $response = $this->getJson('/api/v1/projects')->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Published One');
        $response->assertJsonPath('data.0.technologies.0', 'Laravel');
        $response->assertJsonMissingPath('data.0.challenge');
    }

    public function test_project_show_includes_the_full_narrative(): void
    {
        $project = Project::factory()->create([
            'title' => 'Deep Dive',
            'challenge' => 'The challenge text.',
        ]);

        $this->getJson("/api/v1/projects/{$project->slug}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Deep Dive')
            ->assertJsonPath('data.challenge', 'The challenge text.');
    }

    public function test_project_show_404s_for_unpublished(): void
    {
        $project = Project::factory()->draft()->create();

        $this->getJson("/api/v1/projects/{$project->slug}")->assertNotFound();
    }

    public function test_posts_index_returns_published_posts_only(): void
    {
        Post::factory()->create(['title' => 'Live Post']);
        Post::factory()->draft()->create(['title' => 'Draft Post']);

        $response = $this->getJson('/api/v1/posts')->assertOk();

        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Live Post');
        $response->assertJsonMissingPath('data.0.body_html');
    }

    public function test_post_show_includes_rendered_body(): void
    {
        $post = Post::factory()->create(['body' => '## Heading']);

        $this->getJson("/api/v1/posts/{$post->slug}")
            ->assertOk()
            ->assertJsonPath('data.slug', $post->slug)
            ->assertJsonPath('data.body_html', "<h2>Heading</h2>\n");
    }

    public function test_skills_index_groups_by_category(): void
    {
        $skill = Skill::factory()->create(['name' => 'RabbitMQ', 'level' => 85]);

        $response = $this->getJson('/api/v1/skills')->assertOk();

        $response->assertJsonPath('data.0.name', $skill->category->name);
        $response->assertJsonPath('data.0.skills.0.name', 'RabbitMQ');
    }

    public function test_event_beacon_stores_a_page_view(): void
    {
        $this->optionsJson('/api/v1/events', [
            'type' => 'page_view',
            'path' => '/projects/some-case-study',
            'referrer' => 'https://google.com',
        ])->assertNoContent();

        $this->assertDatabaseHas('analytics_events', [
            'type' => 'page_view',
            'path' => '/projects/some-case-study',
        ]);
    }

    public function test_event_beacon_rejects_an_unknown_type(): void
    {
        $this->optionsJson('/api/v1/events', [
            'type' => 'suspicious_custom_type',
            'path' => '/',
        ])->assertUnprocessable();

        $this->assertDatabaseCount('analytics_events', 0);
    }

    public function test_event_beacon_requires_a_path_starting_with_a_slash(): void
    {
        $this->optionsJson('/api/v1/events', [
            'type' => 'page_view',
            'path' => 'not-a-path',
        ])->assertUnprocessable();
    }

    public function test_event_beacon_is_rate_limited(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $this->optionsJson('/api/v1/events', ['type' => 'page_view', 'path' => '/'])
                ->assertNoContent();
        }

        $this->optionsJson('/api/v1/events', ['type' => 'page_view', 'path' => '/'])
            ->assertStatus(429);

        $this->assertSame(20, AnalyticsEvent::count());
    }

    public function test_api_paths_are_disallowed_in_robots_txt(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /api');
    }
}
