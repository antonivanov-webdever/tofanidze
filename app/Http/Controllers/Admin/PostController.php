<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\ListInput;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::latest('published_at')->latest('id')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.form', ['post' => new Post]);
    }

    public function store(Request $request): RedirectResponse
    {
        $post = Post::create($this->validated($request));

        return redirect()
            ->route('admin.posts.index')
            ->with('status', "Article “{$post->title}” created.");
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $post->update($this->validated($request, $post));

        return redirect()
            ->route('admin.posts.index')
            ->with('status', "Article “{$post->title}” updated.");
    }

    public function destroy(Post $post): RedirectResponse
    {
        $title = $post->title;
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('status', "Article “{$title}” deleted.");
    }

    protected function validated(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'alpha_dash',
                'unique:posts,slug'.($post ? ','.$post->id : '')],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'cover_image' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'tags_text' => ['nullable', 'string'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['tags'] = ListInput::lines(str_replace(',', "\n", (string) $request->input('tags_text')));

        unset($data['tags_text']);

        return $data;
    }
}
