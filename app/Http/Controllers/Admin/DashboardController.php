<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Post;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                ['label' => 'Case studies', 'value' => Project::count(), 'sub' => Project::published()->count().' published', 'icon' => 'grid', 'route' => 'admin.projects.index'],
                ['label' => 'Articles', 'value' => Post::count(), 'sub' => Post::published()->count().' published', 'icon' => 'file-text', 'route' => 'admin.posts.index'],
                ['label' => 'Messages', 'value' => ContactMessage::count(), 'sub' => ContactMessage::unread()->count().' unread', 'icon' => 'inbox', 'route' => 'admin.messages.index'],
                ['label' => 'Technologies', 'value' => Technology::count(), 'sub' => Technology::featured()->count().' featured', 'icon' => 'code', 'route' => 'admin.technologies.index'],
            ],
            'recentMessages' => ContactMessage::latest()->take(5)->get(),
            'recentProjects' => Project::ordered()->take(5)->get(),
            'experienceCount' => Experience::count(),
        ]);
    }
}
