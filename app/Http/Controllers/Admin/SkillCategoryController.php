<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SkillCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SkillCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.skill-categories.index', [
            'categories' => SkillCategory::ordered()->with('skills')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.skill-categories.form', ['category' => new SkillCategory]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = SkillCategory::create($this->validated($request));

        return redirect()
            ->route('admin.skill-categories.index')
            ->with('status', "Group “{$category->name}” created.");
    }

    public function edit(SkillCategory $skillCategory): View
    {
        return view('admin.skill-categories.form', ['category' => $skillCategory]);
    }

    public function update(Request $request, SkillCategory $skillCategory): RedirectResponse
    {
        $skillCategory->update($this->validated($request, $skillCategory));

        return redirect()
            ->route('admin.skill-categories.index')
            ->with('status', "Group “{$skillCategory->name}” updated.");
    }

    public function destroy(SkillCategory $skillCategory): RedirectResponse
    {
        $name = $skillCategory->name;
        $skillCategory->delete();

        return redirect()
            ->route('admin.skill-categories.index')
            ->with('status', "Group “{$name}” and its skills deleted.");
    }

    protected function validated(Request $request, ?SkillCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash',
                Rule::unique('skill_categories', 'slug')->ignore($category)],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:40'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['sort_order'] ??= 0;

        return $data;
    }
}
