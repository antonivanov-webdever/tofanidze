<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Support\ListInput;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(): View
    {
        return view('admin.experiences.index', [
            'experiences' => Experience::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.experiences.form', ['experience' => new Experience]);
    }

    public function store(Request $request): RedirectResponse
    {
        $experience = Experience::create($this->validated($request));

        return redirect()
            ->route('admin.experiences.index')
            ->with('status', "Role at {$experience->company} added.");
    }

    public function edit(Experience $experience): View
    {
        return view('admin.experiences.form', compact('experience'));
    }

    public function update(Request $request, Experience $experience): RedirectResponse
    {
        $experience->update($this->validated($request));

        return redirect()
            ->route('admin.experiences.index')
            ->with('status', "Role at {$experience->company} updated.");
    }

    public function destroy(Experience $experience): RedirectResponse
    {
        $company = $experience->company;
        $experience->delete();

        return redirect()
            ->route('admin.experiences.index')
            ->with('status', "Role at {$company} deleted.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'company' => ['required', 'string', 'max:190'],
            'company_url' => ['nullable', 'url', 'max:255'],
            'position' => ['required', 'string', 'max:190'],
            'location' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['nullable', 'string', 'max:60'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'highlights_text' => ['nullable', 'string'],
            'stack_text' => ['nullable', 'string'],
        ]);

        $data['is_current'] = $request->boolean('is_current');
        $data['sort_order'] ??= 0;
        $data['highlights'] = ListInput::lines($request->input('highlights_text'));
        $data['stack'] = ListInput::lines(str_replace(',', "\n", (string) $request->input('stack_text')));

        if ($data['is_current']) {
            $data['ended_at'] = null;
        }

        unset($data['highlights_text'], $data['stack_text']);

        return $data;
    }
}
