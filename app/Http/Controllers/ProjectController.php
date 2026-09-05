<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Contracts\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::published()->ordered()->with('technologies')->get();

        return view('pages.projects.index', [
            'projects' => $projects,
            'technologies' => Technology::query()
                ->whereHas('projects', fn ($query) => $query->published())
                ->ordered()
                ->get(),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        $project->load('technologies');

        $related = Project::published()
            ->where('id', '!=', $project->id)
            ->whereHas('technologies', fn ($query) => $query->whereIn(
                'technologies.id',
                $project->technologies->pluck('id')
            ))
            ->with('technologies')
            ->ordered()
            ->take(2)
            ->get();

        if ($related->count() < 2) {
            $related = $related->concat(
                Project::published()
                    ->whereNotIn('id', $related->pluck('id')->push($project->id))
                    ->with('technologies')
                    ->ordered()
                    ->take(2 - $related->count())
                    ->get()
            );
        }

        return view('pages.projects.show', compact('project', 'related'));
    }
}
