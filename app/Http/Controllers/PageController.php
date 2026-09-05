<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Project;
use App\Models\SkillCategory;
use App\Models\Technology;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'experiences' => Experience::ordered()->get(),
            'skillCategories' => SkillCategory::ordered()->with('skills')->get(),
            'technologies' => Technology::ordered()->get()->groupBy('category'),
            'projectCount' => Project::published()->count(),
        ]);
    }
}
