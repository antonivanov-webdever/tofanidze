<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Project */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'client' => $this->client,
            'industry' => $this->industry,
            'role' => $this->role,
            'summary' => $this->summary,
            'year' => $this->year,
            'duration' => $this->duration,
            'technologies' => $this->technologies->pluck('name'),
            'url' => route('projects.show', $this->resource),
            $this->mergeWhen($request->routeIs('api.v1.projects.show'), [
                'challenge' => $this->challenge,
                'solution' => $this->solution,
                'outcome' => $this->outcome,
                'metrics' => $this->metrics,
                'highlights' => $this->highlights,
            ]),
        ];
    }
}
