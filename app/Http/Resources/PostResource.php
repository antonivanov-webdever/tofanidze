<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Post */
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'tags' => $this->tags,
            'published_at' => $this->published_at?->toIso8601String(),
            'reading_time_minutes' => $this->readingTime(),
            'url' => route('blog.show', $this->resource),
            $this->mergeWhen($request->routeIs('api.v1.posts.show'), [
                'body_html' => $this->renderedBody(),
            ]),
        ];
    }
}
