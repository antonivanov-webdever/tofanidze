<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection(
            Project::published()->ordered()->with('technologies')->get()
        );
    }

    public function show(Project $project): ProjectResource
    {
        abort_unless($project->is_published, 404);

        return new ProjectResource($project->load('technologies'));
    }
}
