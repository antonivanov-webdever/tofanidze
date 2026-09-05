<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.skill-categories.index');
    }

    public function create(Request $request): View
    {
        return view('admin.skills.form', [
            'skill' => new Skill(['skill_category_id' => $request->integer('category')]),
            'categories' => SkillCategory::ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $skill = Skill::create($this->validated($request));

        return redirect()
            ->route('admin.skill-categories.index')
            ->with('status', "Skill “{$skill->name}” added.");
    }

    public function edit(Skill $skill): View
    {
        return view('admin.skills.form', [
            'skill' => $skill,
            'categories' => SkillCategory::ordered()->get(),
        ]);
    }

    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $skill->update($this->validated($request));

        return redirect()
            ->route('admin.skill-categories.index')
            ->with('status', "Skill “{$skill->name}” updated.");
    }

    public function destroy(Skill $skill): RedirectResponse
    {
        $name = $skill->name;
        $skill->delete();

        return redirect()
            ->route('admin.skill-categories.index')
            ->with('status', "Skill “{$name}” deleted.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'skill_category_id' => ['required', 'exists:skill_categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'level' => ['required', 'integer', 'min:1', 'max:100'],
            'years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'note' => ['nullable', 'string', 'max:190'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['sort_order'] ??= 0;

        return $data;
    }
}
