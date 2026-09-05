<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'client',
        'industry',
        'role',
        'summary',
        'challenge',
        'solution',
        'outcome',
        'metrics',
        'highlights',
        'cover_image',
        'external_url',
        'repository_url',
        'year',
        'duration',
        'team_size',
        'is_featured',
        'is_published',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'metrics' => 'array',
            'highlights' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if (blank($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class)->ordered();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('year')->orderByDesc('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function coverUrl(): ?string
    {
        if (blank($this->cover_image)) {
            return null;
        }

        return Str::startsWith($this->cover_image, ['http://', 'https://', '/'])
            ? $this->cover_image
            : asset('storage/'.$this->cover_image);
    }
}
