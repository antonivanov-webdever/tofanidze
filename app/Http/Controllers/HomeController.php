<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Technology;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'featuredProjects' => Project::published()->featured()->ordered()
                ->with('technologies')
                ->take(4)
                ->get(),
            'services' => Service::published()->ordered()->get(),
            'experiences' => Experience::ordered()->take(3)->get(),
            'technologies' => Technology::featured()->ordered()->get()->groupBy('category'),
            'marqueeTechnologies' => Technology::ordered()->get(),
            'posts' => Post::published()->ordered()->take(3)->get(),
            'projectCount' => Project::published()->count(),
        ]);
    }
}
