<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'monthly'],
            ['loc' => route('about'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => route('projects.index'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['loc' => route('blog.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => route('resume.show'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => route('contact.show'), 'priority' => '0.7', 'changefreq' => 'yearly'],
        ]);

        $urls = $urls->concat(
            Project::published()->ordered()->get()->map(fn (Project $project) => [
                'loc' => route('projects.show', $project),
                'lastmod' => $project->updated_at?->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'monthly',
            ])
        )->concat(
            Post::published()->ordered()->get()->map(fn (Post $post) => [
                'loc' => route('blog.show', $post),
                'lastmod' => $post->updated_at?->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ])
        );

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}
