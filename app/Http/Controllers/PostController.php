<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $tag = $request->string('tag')->toString();

        $posts = Post::published()
            ->when($tag, fn ($query) => $query->where('tags', 'like', '%"'.$tag.'"%'))
            ->ordered()
            ->paginate(9)
            ->withQueryString();

        $tags = Post::published()
            ->pluck('tags')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('pages.blog.index', compact('posts', 'tags', 'tag'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        $post->incrementQuietly('views');

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->ordered()
            ->take(2)
            ->get();

        return view('pages.blog.show', compact('post', 'related'));
    }
}
