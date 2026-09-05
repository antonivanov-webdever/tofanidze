<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Technology;
use App\Models\User;
use App\Support\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create();
    }

    public function test_guests_are_redirected_to_the_login_screen(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.projects.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.settings.edit'))->assertRedirect(route('admin.login'));
    }

    public function test_a_valid_login_reaches_the_dashboard(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-horse')]);

        $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'correct-horse',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_an_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-horse')]);

        $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'wrong',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_every_admin_screen_renders(): void
    {
        $this->actingAs($this->admin());

        $project = Project::factory()->create();
        $post = Post::factory()->create();
        $message = ContactMessage::factory()->create();

        $screens = [
            route('admin.dashboard'),
            route('admin.projects.index'),
            route('admin.projects.create'),
            route('admin.projects.edit', $project),
            route('admin.posts.index'),
            route('admin.posts.create'),
            route('admin.posts.edit', $post),
            route('admin.experiences.index'),
            route('admin.experiences.create'),
            route('admin.services.index'),
            route('admin.services.create'),
            route('admin.technologies.index'),
            route('admin.technologies.create'),
            route('admin.skill-categories.index'),
            route('admin.skill-categories.create'),
            route('admin.skills.create'),
            route('admin.messages.index'),
            route('admin.messages.show', $message),
            route('admin.settings.edit'),
        ];

        foreach ($screens as $screen) {
            $this->get($screen)->assertOk();
        }
    }

    public function test_it_creates_a_project_with_list_fields_and_technologies(): void
    {
        $this->actingAs($this->admin());
        $technology = Technology::factory()->create(['name' => 'Laravel']);

        $this->post(route('admin.projects.store'), [
            'title' => 'Deal Registration Portal',
            'summary' => 'A portal where resellers register deals that sync straight into Salesforce.',
            'challenge' => 'Spreadsheets.',
            'solution' => 'A queue-backed portal.',
            'metrics_text' => "600+ | active partners\n<2 min | sync latency",
            'highlights_text' => "Field-level RBAC\nIdempotent sync",
            'technologies' => [$technology->id],
            'is_published' => '1',
            'is_featured' => '1',
        ])->assertRedirect(route('admin.projects.index'));

        $project = Project::sole();

        $this->assertSame('deal-registration-portal', $project->slug);
        $this->assertSame([
            ['value' => '600+', 'label' => 'active partners'],
            ['value' => '<2 min', 'label' => 'sync latency'],
        ], $project->metrics);
        $this->assertSame(['Field-level RBAC', 'Idempotent sync'], $project->highlights);
        $this->assertTrue($project->technologies->contains($technology));
        $this->assertTrue($project->is_published);
    }

    public function test_it_updates_and_deletes_a_project(): void
    {
        $this->actingAs($this->admin());
        $project = Project::factory()->create(['title' => 'Old Title']);

        $this->put(route('admin.projects.update', $project), [
            'title' => 'New Title',
            'slug' => $project->slug,
            'summary' => $project->summary,
            'is_published' => '1',
        ])->assertRedirect(route('admin.projects.index'));

        $this->assertSame('New Title', $project->fresh()->title);

        $this->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertModelMissing($project);
    }

    public function test_it_rejects_a_duplicate_project_slug(): void
    {
        $this->actingAs($this->admin());
        Project::factory()->create(['slug' => 'taken-slug']);

        $this->post(route('admin.projects.store'), [
            'title' => 'Another Project',
            'slug' => 'taken-slug',
            'summary' => 'A summary long enough to pass validation without any trouble.',
        ])->assertSessionHasErrors('slug');
    }

    public function test_publishing_an_article_stamps_the_publish_date(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.posts.store'), [
            'title' => 'Queue Topologies That Survive Rate Limits',
            'body' => '## Retry, delay, dead-letter',
            'tags_text' => 'RabbitMQ, Architecture',
            'is_published' => '1',
        ])->assertRedirect(route('admin.posts.index'));

        $post = Post::sole();

        $this->assertNotNull($post->published_at);
        $this->assertSame(['RabbitMQ', 'Architecture'], $post->tags);
    }

    public function test_opening_a_message_marks_it_as_read(): void
    {
        $this->actingAs($this->admin());
        $message = ContactMessage::factory()->create();

        $this->assertTrue($message->isUnread());

        $this->get(route('admin.messages.show', $message))->assertOk();

        $this->assertFalse($message->fresh()->isUnread());
    }

    public function test_settings_are_saved_and_take_effect_on_the_site(): void
    {
        $this->actingAs($this->admin());

        $this->put(route('admin.settings.update'), [
            'settings' => [
                'name' => 'Anton T.',
                'headline' => 'Principal Engineer',
                'email' => 'hello@example.com',
                'available_for_work' => '1',
                'years_experience' => '7',
            ],
        ])->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('Anton T.', Setting::get('name'));

        app(Site::class)->forget();

        $this->get('/')->assertOk()->assertSee('Principal Engineer');
    }

    public function test_signing_out_returns_to_the_public_site(): void
    {
        $this->actingAs($this->admin());

        $this->post(route('admin.logout'))->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
