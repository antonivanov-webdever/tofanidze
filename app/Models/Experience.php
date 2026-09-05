<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'company',
        'company_url',
        'position',
        'location',
        'employment_type',
        'started_at',
        'ended_at',
        'is_current',
        'description',
        'highlights',
        'stack',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ended_at' => 'date',
            'is_current' => 'boolean',
            'highlights' => 'array',
            'stack' => 'array',
        ];
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('started_at');
    }

    public function period(): string
    {
        $start = $this->started_at?->format('M Y') ?? '';
        $end = $this->is_current ? 'Present' : ($this->ended_at?->format('M Y') ?? '');

        return trim("{$start} — {$end}", ' —');
    }

    public function durationInMonths(): int
    {
        $end = $this->is_current ? now() : ($this->ended_at ?? now());

        return max(1, (int) $this->started_at->diffInMonths($end));
    }

    public function durationLabel(): string
    {
        $months = $this->durationInMonths();
        $years = intdiv($months, 12);
        $rest = $months % 12;

        return trim(($years ? "{$years} yr " : '').($rest ? "{$rest} mo" : '')) ?: '1 mo';
    }
}
