<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Technology;
use App\Support\ListInput;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::ordered()->with('technologies')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => new Project(['is_published' => true, 'year' => now()->year]),
            'technologies' => Technology::ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = Project::create($this->validated($request));
        $project->technologies()->sync($request->input('technologies', []));

        return redirect()
            ->route('admin.projects.index')
            ->with('status', "Case study “{$project->title}” created.");
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', [
            'project' => $project->load('technologies'),
            'technologies' => Technology::ordered()->get(),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validated($request, $project));
        $project->technologies()->sync($request->input('technologies', []));

        return redirect()
            ->route('admin.projects.index')
            ->with('status', "Case study “{$project->title}” updated.");
    }

    public function destroy(Project $project): RedirectResponse
    {
        $title = $project->title;
        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('status', "Case study “{$title}” deleted.");
    }

    protected function validated(Request $request, ?Project $project = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'alpha_dash',
                'unique:projects,slug'.($project ? ','.$project->id : '')],
            'client' => ['nullable', 'string', 'max:190'],
            'industry' => ['nullable', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'summary' => ['required', 'string', 'max:500'],
            'challenge' => ['nullable', 'string'],
            'solution' => ['nullable', 'string'],
            'outcome' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:'.(now()->year + 1)],
            'duration' => ['nullable', 'string', 'max:60'],
            'team_size' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'highlights_text' => ['nullable', 'string'],
            'metrics_text' => ['nullable', 'string'],
            'technologies' => ['array'],
            'technologies.*' => ['integer', 'exists:technologies,id'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] ??= 0;
        $data['highlights'] = ListInput::lines($request->input('highlights_text'));
        $data['metrics'] = ListInput::pairs($request->input('metrics_text'), 'value', 'label');

        unset($data['highlights_text'], $data['metrics_text'], $data['technologies']);

        return $data;
    }
}
