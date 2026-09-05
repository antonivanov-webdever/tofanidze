<x-layout
    title="Case Studies"
    description="B2B marketing and enterprise engineering case studies: partner portals, attribution dashboards, and Salesforce, Marketo and HubSpot integrations."
>
    <x-page-header
        eyebrow="Selected work"
        title="Case studies"
        lead="Each one covers the same three things: what was actually broken, the architecture that fixed it, and what changed for the business afterwards."
    />

    @php
        $filterData = $projects->map(fn ($project) => [
            'tech' => $project->technologies->pluck('slug')->implode('|'),
            'haystack' => Str::lower(implode(' ', [
                $project->title,
                $project->summary,
                $project->industry,
                $project->client,
                $project->technologies->pluck('name')->implode(' '),
            ])),
        ])->values();
    @endphp

    <div class="mx-auto max-w-6xl px-5 pb-20 sm:px-8" x-data="projectFilter(@js($filterData))">
        {{-- Filters --}}
        <div class="panel mb-8 p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button"
                            @click="active = 'all'"
                            :class="active === 'all'
                                ? 'border-accent-400/50 bg-accent-500/15 text-white'
                                : 'border-white/10 bg-white/[0.03] text-muted hover:text-white'"
                            class="rounded-lg border px-3 py-1.5 text-xs font-medium transition">
                        All work
                    </button>

                    @foreach ($technologies as $technology)
                        <button type="button"
                                @click="active = '{{ $technology->slug }}'"
                                :class="active === '{{ $technology->slug }}'
                                    ? 'border-accent-400/50 bg-accent-500/15 text-white'
                                    : 'border-white/10 bg-white/[0.03] text-muted hover:text-white'"
                                class="rounded-lg border px-3 py-1.5 font-mono text-xs transition">
                            {{ $technology->name }}
                        </button>
                    @endforeach
                </div>

                <div class="relative shrink-0 lg:w-64">
                    <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" />
                    <input type="search"
                           x-model="query"
                           placeholder="Search case studies"
                           aria-label="Search case studies"
                           class="field !py-2.5 !pl-9 !text-sm">
                </div>
            </div>

            <div x-show="isFiltered" x-cloak class="mt-4 flex items-center gap-3 border-t border-white/8 pt-4">
                <span class="text-xs text-muted">Filtered view</span>
                <button type="button" @click="reset()" class="text-xs font-semibold text-accent-300 hover:underline">
                    Clear filters
                </button>
            </div>
        </div>

        {{-- Grid --}}
        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ($projects as $project)
                <div x-show="matches(@js($filterData[$loop->index]['tech']), @js($filterData[$loop->index]['haystack']))"
                     x-transition.opacity>
                    <x-project-card :project="$project" featured />
                </div>
            @endforeach
        </div>

        <p x-show="visibleCount === 0"
           x-cloak
           class="panel mt-6 p-10 text-center text-sm text-muted">
            No case studies match that filter.
            <button type="button" @click="reset()" class="ml-1 font-semibold text-accent-300 hover:underline">
                Show everything
            </button>
        </p>

        <div class="panel mt-10 flex flex-col items-center gap-4 p-8 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="text-lg font-semibold text-white">Working on something similar?</h2>
                <p class="mt-1 text-sm text-muted">
                    Happy to talk through the approach before you commit to anything.
                </p>
            </div>
            <a href="{{ route('contact.show') }}" class="btn-primary shrink-0">
                Get in touch
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>
    </div>
</x-layout>
