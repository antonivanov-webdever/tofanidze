<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::published()->ordered()->take(20)->get();

        return response()
            ->view('feed', compact('posts'))
            ->header('Content-Type', 'application/xml');
    }
}
