<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TechnologyController extends Controller
{
    public function index(): View
    {
        return view('admin.technologies.index', [
            'technologies' => Technology::ordered()->withCount('projects')->get()->groupBy('category'),
        ]);
    }

    public function create(): View
    {
        return view('admin.technologies.form', ['technology' => new Technology]);
    }

    public function store(Request $request): RedirectResponse
    {
        $technology = Technology::create($this->validated($request));

        return redirect()
            ->route('admin.technologies.index')
            ->with('status', "{$technology->name} added.");
    }

    public function edit(Technology $technology): View
    {
        return view('admin.technologies.form', compact('technology'));
    }

    public function update(Request $request, Technology $technology): RedirectResponse
    {
        $technology->update($this->validated($request, $technology));

        return redirect()
            ->route('admin.technologies.index')
            ->with('status', "{$technology->name} updated.");
    }

    public function destroy(Technology $technology): RedirectResponse
    {
        $name = $technology->name;
        $technology->delete();

        return redirect()
            ->route('admin.technologies.index')
            ->with('status', "{$name} deleted.");
    }

    protected function validated(Request $request, ?Technology $technology = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'slug' => ['nullable', 'string', 'max:60', 'alpha_dash',
                Rule::unique('technologies', 'slug')->ignore($technology)],
            'category' => ['required', Rule::in(array_keys(Technology::CATEGORIES))],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order'] ??= 0;

        return $data;
    }
}
