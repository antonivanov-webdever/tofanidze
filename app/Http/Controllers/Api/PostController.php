<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::published()->ordered()->get()
        );
    }

    public function show(Post $post): PostResource
    {
        abort_unless($post->is_published && $post->published_at?->isPast(), 404);

        return new PostResource($post);
    }
}
