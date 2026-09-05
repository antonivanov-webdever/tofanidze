<x-layout>
    {{-- ───────────────────────── Hero ───────────────────────── --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-grid" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -top-40 left-1/2 h-[520px] w-[820px] -translate-x-1/2 glow-accent opacity-50"
             aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-5 pb-16 pt-16 sm:px-8 sm:pb-24 sm:pt-24">
            <div class="grid items-center gap-14 lg:grid-cols-[1.15fr_1fr]">
                <div class="animate-fade-up">
                    @if ($site->get('available_for_work'))
                        <p class="inline-flex items-center gap-2 rounded-full border border-emerald-400/25 bg-emerald-400/10
                                  px-3.5 py-1.5 text-xs font-medium text-emerald-300">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                            </span>
                            {{ $site->get('availability_note') }}
                        </p>
                    @endif

                    <h1 class="mt-6 text-4xl font-bold leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ $site->name() }} —<br>
                        <span class="text-gradient">{{ $site->get('headline') }}</span>
                    </h1>

                    <p class="mt-4 font-mono text-sm text-accent-300">
                        {{ $site->get('tagline') }}
                    </p>

                    <p class="mt-6 max-w-xl text-base leading-relaxed text-muted sm:text-[17px]">
                        {{ $site->get('intro') }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-3">
                        <a href="{{ route('contact.show') }}" class="btn-primary">
                            Start a project
                            <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn-ghost">
                            View case studies
                        </a>
                        <a href="{{ route('resume.download') }}" class="btn-ghost !border-transparent !bg-transparent !px-3">
                            <x-icon name="download" class="h-4 w-4" />
                            Résumé
                        </a>
                    </div>

                    <dl class="mt-12 grid max-w-lg grid-cols-3 gap-6 border-t border-white/8 pt-8">
                        @foreach ([
                            ['value' => $site->get('years_experience').'+', 'label' => 'years in production'],
                            ['value' => $projectCount > 0 ? $projectCount.'+' : '—', 'label' => 'case studies shipped'],
                            ['value' => '3', 'label' => 'CRM platforms integrated'],
                        ] as $stat)
                            <div>
                                <dt class="sr-only">{{ $stat['label'] }}</dt>
                                <dd>
                                    <span class="block font-mono text-2xl font-semibold text-white sm:text-3xl">
                                        {{ $stat['value'] }}
                                    </span>
                                    <span class="mt-1.5 block text-xs leading-tight text-muted">{{ $stat['label'] }}</span>
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                {{-- Integration diagram: the specialisation, shown rather than claimed. --}}
                <div class="animate-fade-up [animation-delay:150ms]">
                    <div class="panel relative overflow-hidden p-6 sm:p-7">
                        <div class="flex items-center justify-between border-b border-white/8 pb-4">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-red-400/70"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-400/70"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/70"></span>
                            </div>
                            <span class="font-mono text-[11px] text-muted">integration-layer.yaml</span>
                        </div>

                        <div class="mt-6 space-y-3">
                            <div class="rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3">
                                <p class="font-mono text-[11px] uppercase tracking-wider text-muted">Source</p>
                                <p class="mt-1 text-sm font-semibold text-white">Portal · Dashboard · Product events</p>
                            </div>

                            <div class="flex items-center justify-center py-1">
                                <span class="h-6 w-px bg-gradient-to-b from-accent-400/70 to-accent-400/10"></span>
                            </div>

                            <div class="rounded-xl border border-accent-400/30 bg-accent-500/10 px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-mono text-[11px] uppercase tracking-wider text-accent-300">Queue</p>
                                        <p class="mt-1 text-sm font-semibold text-white">RabbitMQ · idempotent · retryable</p>
                                    </div>
                                    <x-icon name="refresh" class="h-5 w-5 text-accent-300" />
                                </div>
                            </div>

                            <div class="flex items-center justify-center py-1">
                                <span class="h-6 w-px bg-gradient-to-b from-accent-400/70 to-accent-400/10"></span>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['Salesforce', 'Marketo', 'HubSpot'] as $platform)
                                    <div class="rounded-lg border border-white/10 bg-white/[0.04] px-2 py-3 text-center">
                                        <p class="font-mono text-[11px] font-medium text-muted-strong">{{ $platform }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-2 border-t border-white/8 pt-4">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-accent-400 opacity-60"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-accent-400"></span>
                            </span>
                            <span class="font-mono text-[11px] text-muted">
                                no duplicates · no silent drops · replayable
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ───────────────────────── Tech marquee ───────────────────────── --}}
    <div class="relative overflow-hidden border-y border-white/8 bg-ink-900/40 py-5">
        <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-24 bg-gradient-to-r from-ink-950 to-transparent"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-24 bg-gradient-to-l from-ink-950 to-transparent"></div>

        <div class="flex w-max marquee-track">
            @foreach ([1, 2] as $pass)
                <ul class="flex items-center gap-8 pr-8" @if ($pass === 2) aria-hidden="true" @endif>
                    @foreach ($marqueeTechnologies as $technology)
                        <li class="flex items-center gap-2 whitespace-nowrap">
                            <span class="h-1.5 w-1.5 rounded-full"
                                  style="background-color: {{ $technology->color ?? '#4f9dff' }}"></span>
                            <span class="font-mono text-sm text-muted">{{ $technology->name }}</span>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>

    {{-- ───────────────────────── Services ───────────────────────── --}}
    <x-section
        eyebrow="What I build"
        title="Systems B2B marketing teams actually run on"
        lead="Six years of shipping the unglamorous parts: the portal your partners log into, the dashboard leadership trusts, and the sync that keeps both honest."
    >
        <div class="grid gap-5 sm:grid-cols-2">
            @foreach ($services as $service)
                <div x-data="reveal({{ $loop->index * 80 }})"
                     x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     class="panel panel-hover group p-7 transition-all duration-700">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-accent-400/25
                                bg-accent-500/10 text-accent-300 transition group-hover:border-accent-400/50">
                        <x-icon :name="$service->icon" class="h-5 w-5" />
                    </div>

                    <h3 class="mt-5 text-lg font-semibold tracking-tight text-white">{{ $service->title }}</h3>

                    @if ($service->subtitle)
                        <p class="mt-1 font-mono text-xs text-accent-300">{{ $service->subtitle }}</p>
                    @endif

                    <p class="mt-4 text-sm leading-relaxed text-muted">{{ $service->description }}</p>

                    @if ($service->bullets)
                        <ul class="mt-5 space-y-2 border-t border-white/8 pt-5">
                            @foreach ($service->bullets as $bullet)
                                <li class="flex gap-2.5 text-sm text-muted-strong">
                                    <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-accent-400" />
                                    <span>{{ $bullet }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </x-section>

    {{-- ───────────────────────── Featured work ───────────────────────── --}}
    <x-section eyebrow="Selected work" title="Case studies" class="!pt-0">
        <div class="mb-10 flex items-end justify-between gap-6">
            <p class="max-w-xl text-base leading-relaxed text-muted">
                The problem, the architecture behind the fix, and what changed for the business afterwards.
            </p>
            <a href="{{ route('projects.index') }}"
               class="hidden shrink-0 items-center gap-2 text-sm font-semibold text-white transition hover:gap-3 hover:text-accent-300 sm:inline-flex">
                All case studies
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            @foreach ($featuredProjects as $project)
                <x-project-card :project="$project" featured />
            @endforeach
        </div>

        <div class="mt-8 sm:hidden">
            <a href="{{ route('projects.index') }}" class="btn-ghost w-full">All case studies</a>
        </div>
    </x-section>

    {{-- ───────────────────────── Stack ───────────────────────── --}}
    <x-section eyebrow="Toolkit" title="The stack I reach for" class="!pt-0">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($technologies as $category => $items)
                <div class="panel p-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-accent-300">
                        {{ \App\Models\Technology::CATEGORIES[$category] ?? Str::headline($category) }}
                    </h3>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($items as $technology)
                            <li class="flex items-center gap-2.5 text-sm text-muted-strong">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full"
                                      style="background-color: {{ $technology->color ?? '#4f9dff' }}"></span>
                                {{ $technology->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <p class="mt-6 text-sm text-muted">
            Full breakdown, including the legacy stacks I maintain, on the
            <a href="{{ route('about') }}" class="text-accent-300 hover:underline">about page</a>.
        </p>
    </x-section>

    {{-- ───────────────────────── Experience ───────────────────────── --}}
    <x-section eyebrow="Track record" title="Where I've worked" class="!pt-0">
        <ol class="mt-2">
            @foreach ($experiences as $experience)
                <x-timeline-entry :experience="$experience" />
            @endforeach
        </ol>

        <a href="{{ route('about') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-white transition hover:gap-3 hover:text-accent-300">
            Full background and skills
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </x-section>

    {{-- ───────────────────────── Writing ───────────────────────── --}}
    @if ($posts->isNotEmpty())
        <x-section eyebrow="Writing" title="Notes from the integration layer" class="!pt-0">
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-semibold text-white transition hover:gap-3 hover:text-accent-300">
                    All articles
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
        </x-section>
    @endif

    {{-- ───────────────────────── CTA ───────────────────────── --}}
    <section class="mx-auto max-w-6xl px-5 pb-8 sm:px-8">
        <div class="panel relative overflow-hidden px-6 py-14 text-center sm:px-14">
            <div class="pointer-events-none absolute inset-0 bg-grid opacity-60" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -bottom-32 left-1/2 h-72 w-[640px] -translate-x-1/2 glow-accent opacity-40"
                 aria-hidden="true"></div>

            <div class="relative">
                <h2 class="mx-auto max-w-2xl text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                    Got a portal, dashboard or integration that needs
                    <span class="text-gradient">to work properly?</span>
                </h2>

                <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-muted">
                    Tell me what is breaking or what you are planning. You will get a straight answer about scope,
                    approach and whether I am the right person for it — usually within one business day.
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('contact.show') }}" class="btn-primary">
                        Start a conversation
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                    <a href="mailto:{{ $site->get('email') }}" class="btn-ghost">
                        <x-icon name="mail" class="h-4 w-4" />
                        {{ $site->get('email') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
