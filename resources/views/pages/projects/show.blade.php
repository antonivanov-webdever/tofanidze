<x-layout
    :title="$project->meta_title ?: $project->title"
    :description="$project->meta_description ?: $project->summary"
    :image="$project->coverUrl()"
    type="article"
>
    @push('head')
        <script type="application/ld+json">
            {!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'CreativeWork',
                'name' => $project->title,
                'about' => $project->summary,
                'url' => route('projects.show', $project),
                'dateCreated' => $project->year ? (string) $project->year : null,
                'creator' => ['@type' => 'Person', 'name' => $site->name()],
                'keywords' => $project->technologies->pluck('name')->implode(', '),
            ]), JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush

    {{-- ───────────────── Header ───────────────── --}}
    <section class="relative overflow-hidden border-b border-white/8">
        <div class="pointer-events-none absolute inset-0 bg-grid opacity-70" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -top-40 right-0 h-80 w-[560px] glow-accent opacity-30" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-4xl px-5 py-14 sm:px-8 sm:py-20">
            <a href="{{ route('projects.index') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-muted transition hover:text-white">
                <x-icon name="arrow-left" class="h-4 w-4" />
                All case studies
            </a>

            <div class="mt-8 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs">
                @if ($project->industry)
                    <span class="chip !text-accent-300">{{ $project->industry }}</span>
                @endif
                @if ($project->year)
                    <span class="chip font-mono">{{ $project->year }}</span>
                @endif
                @if ($project->duration)
                    <span class="chip">
                        <x-icon name="clock" class="h-3.5 w-3.5" />
                        {{ $project->duration }}
                    </span>
                @endif
            </div>

            <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-white sm:text-5xl">
                {{ $project->title }}
            </h1>

            <p class="mt-5 max-w-2xl text-lg leading-relaxed text-muted-strong">
                {{ $project->summary }}
            </p>

            <dl class="mt-10 grid gap-x-8 gap-y-5 border-t border-white/8 pt-8 sm:grid-cols-3">
                @foreach (array_filter([
                    'Client' => $project->client,
                    'My role' => $project->role,
                    'Team' => $project->team_size,
                ]) as $label => $value)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-muted">{{ $label }}</dt>
                        <dd class="mt-1.5 text-sm font-medium text-white">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($project->external_url || $project->repository_url)
                <div class="mt-8 flex flex-wrap gap-3">
                    @if ($project->external_url)
                        <a href="{{ $project->external_url }}" target="_blank" rel="noopener noreferrer" class="btn-ghost !py-2.5 !text-sm">
                            <x-icon name="external" class="h-4 w-4" />
                            Visit project
                        </a>
                    @endif
                    @if ($project->repository_url)
                        <a href="{{ $project->repository_url }}" target="_blank" rel="noopener noreferrer" class="btn-ghost !py-2.5 !text-sm">
                            <x-icon name="github" class="h-4 w-4" />
                            Source
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($project->coverUrl())
        <div class="mx-auto max-w-5xl px-5 pt-12 sm:px-8">
            <img src="{{ $project->coverUrl() }}" alt="{{ $project->title }}"
                 class="w-full rounded-2xl border border-white/10">
        </div>
    @endif

    {{-- ───────────────── Metrics ───────────────── --}}
    @if ($project->metrics)
        <div class="mx-auto max-w-4xl px-5 pt-14 sm:px-8">
            <dl class="grid gap-4 sm:grid-cols-3">
                @foreach ($project->metrics as $metric)
                    <div class="panel p-6">
                        <dt class="text-xs leading-tight text-muted">{{ $metric['label'] }}</dt>
                        <dd class="mt-2 font-mono text-2xl font-semibold text-gradient">{{ $metric['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif

    {{-- ───────────────── Narrative ───────────────── --}}
    <div class="mx-auto max-w-4xl px-5 py-14 sm:px-8">
        <div class="space-y-12">
            @foreach (array_filter([
                ['icon' => 'target', 'title' => 'The challenge', 'body' => $project->challenge],
                ['icon' => 'layers', 'title' => 'The solution', 'body' => $project->solution],
                ['icon' => 'zap', 'title' => 'The outcome', 'body' => $project->outcome],
            ], fn ($block) => filled($block['body'])) as $block)
                <section>
                    <h2 class="flex items-center gap-3 text-xl font-semibold tracking-tight text-white">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-accent-400/25
                                     bg-accent-500/10 text-accent-300">
                            <x-icon :name="$block['icon']" class="h-4.5 w-4.5" />
                        </span>
                        {{ $block['title'] }}
                    </h2>

                    <div class="mt-5 space-y-4 text-[15.5px] leading-[1.75] text-muted-strong">
                        @foreach (preg_split('/\n\s*\n/', trim($block['body'])) as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </section>
            @endforeach

            @if ($project->highlights)
                <section class="panel p-7">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-accent-300">Technical highlights</h2>
                    <ul class="mt-5 space-y-3">
                        @foreach ($project->highlights as $highlight)
                            <li class="flex gap-3 text-[15px] leading-relaxed text-muted-strong">
                                <x-icon name="check" class="mt-1 h-4 w-4 shrink-0 text-accent-400" />
                                <span>{{ $highlight }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($project->technologies->isNotEmpty())
                <section>
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-muted">Stack</h2>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($project->technologies as $technology)
                            <span class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/[0.04]
                                         px-3 py-1.5 font-mono text-xs text-muted-strong">
                                <span class="h-1.5 w-1.5 rounded-full"
                                      style="background-color: {{ $technology->color ?? '#4f9dff' }}"></span>
                                {{ $technology->name }}
                            </span>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    {{-- ───────────────── Related + CTA ───────────────── --}}
    @if ($related->isNotEmpty())
        <div class="mx-auto max-w-6xl px-5 pb-6 sm:px-8">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-muted">More work</h2>
            <div class="mt-6 grid gap-5 lg:grid-cols-2">
                @foreach ($related as $relatedProject)
                    <x-project-card :project="$relatedProject" />
                @endforeach
            </div>
        </div>
    @endif

    <div class="mx-auto max-w-6xl px-5 py-12 sm:px-8">
        <div class="panel flex flex-col items-center gap-5 p-8 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="text-lg font-semibold text-white">Facing a similar problem?</h2>
                <p class="mt-1 max-w-lg text-sm text-muted">
                    Tell me where it hurts — integrations dropping records, a dashboard nobody trusts, or a legacy
                    portal you cannot safely touch.
                </p>
            </div>
            <a href="{{ route('contact.show') }}" class="btn-primary shrink-0">
                Start a conversation
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>
    </div>
</x-layout>
