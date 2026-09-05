<x-layout
    title="About"
    description="Six years of full-stack engineering across PHP, Node.js, Vue and React — focused on B2B marketing platforms, enterprise portals and CRM integrations."
>
    <x-page-header
        eyebrow="About"
        title="Engineer for the systems marketing runs on"
        :lead="$site->get('summary')"
    />

    {{-- ───────────────── Profile ───────────────── --}}
    <section class="mx-auto max-w-6xl px-5 py-16 sm:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.5fr_1fr]">
            <div class="space-y-5 text-[15.5px] leading-[1.75] text-muted-strong">
                <h2 class="text-2xl font-semibold tracking-tight text-white">How I work</h2>

                <p>
                    Most of my work starts the same way: something already exists, it is important, and it is not
                    behaving. A portal that grew past its data model. A dashboard the sales team stopped believing.
                    An integration that drops records quietly enough that nobody notices for a week.
                </p>

                <p>
                    I like that work. It rewards reading code carefully before changing it, and it forces you to be
                    honest about what you actually know versus what you assume. Before I propose an architecture I want
                    to see the data, the traffic pattern and the failure log — the shape of the problem usually
                    decides the design.
                </p>

                <p>
                    In practice that means I build in slices that ship. A migration that reaches production every two
                    weeks with a fallback path beats a rewrite that lands in one terrifying evening. Integrations get
                    idempotency, retries and a replay path before they get features, because the second call is the
                    one that hurts. And anything a non-engineer has to operate — routing rules, field mappings,
                    content — belongs in configuration, not in a deployment.
                </p>

                <p>
                    I have worked remotely for {{ $site->get('years_experience') }} years across agency, product and
                    consulting teams. I write English fluently, document decisions as I go, and would rather ask an
                    uncomfortable question in week one than discover the answer in week ten.
                </p>
            </div>

            <aside class="space-y-4">
                <div class="panel p-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-muted">At a glance</h3>
                    <dl class="mt-5 space-y-4">
                        @foreach (array_filter([
                            'Experience' => $site->get('years_experience').'+ years',
                            'Case studies' => $projectCount.' published',
                            'Focus' => 'B2B marketing & enterprise',
                            'Location' => $site->get('location'),
                            'Time zone' => $site->get('timezone_label'),
                        ]) as $label => $value)
                            <div class="flex items-baseline justify-between gap-4 border-b border-white/8 pb-3 last:border-0 last:pb-0">
                                <dt class="text-sm text-muted">{{ $label }}</dt>
                                <dd class="text-right text-sm font-medium text-white">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="panel p-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-muted">Get in touch</h3>
                    <div class="mt-5 space-y-3">
                        <a href="mailto:{{ $site->get('email') }}"
                           class="flex items-center gap-3 text-sm text-muted-strong transition hover:text-white">
                            <x-icon name="mail" class="h-4 w-4 text-accent-300" />
                            {{ $site->get('email') }}
                        </a>
                        @foreach ($site->socialLinks() as $network => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-3 text-sm text-muted-strong transition hover:text-white">
                                <x-icon :name="$network" class="h-4 w-4 text-accent-300" />
                                {{ ucfirst($network) }}
                            </a>
                        @endforeach
                    </div>

                    <a href="{{ route('resume.download') }}" class="btn-primary mt-6 w-full !py-2.5 !text-sm">
                        <x-icon name="download" class="h-4 w-4" />
                        Download résumé
                    </a>
                </div>
            </aside>
        </div>
    </section>

    {{-- ───────────────── Skills ───────────────── --}}
    <x-section eyebrow="Capabilities" title="Skills in depth" class="!py-16">
        <div class="grid gap-5 md:grid-cols-2">
            @foreach ($skillCategories as $category)
                <div x-data="reveal({{ $loop->index * 70 }})"
                     x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
                     class="panel p-7 transition-all duration-700">
                    <div class="flex items-start gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                                     border border-accent-400/25 bg-accent-500/10 text-accent-300">
                            <x-icon :name="$category->icon ?: 'code'" class="h-4.5 w-4.5" />
                        </span>
                        <div>
                            <h3 class="text-base font-semibold text-white">{{ $category->name }}</h3>
                            @if ($category->description)
                                <p class="mt-1 text-sm leading-relaxed text-muted">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>

                    <ul class="mt-6 space-y-4">
                        @foreach ($category->skills as $skill)
                            <li>
                                <div class="flex items-baseline justify-between gap-4">
                                    <span class="text-sm font-medium text-muted-strong">{{ $skill->name }}</span>
                                    @if ($skill->years)
                                        <span class="shrink-0 font-mono text-[11px] text-muted">{{ $skill->years }} yr</span>
                                    @endif
                                </div>
                                <div class="mt-2 h-1 overflow-hidden rounded-full bg-white/8"
                                     role="meter"
                                     aria-valuenow="{{ $skill->level }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"
                                     aria-label="{{ $skill->name }} proficiency">
                                    <div class="h-full rounded-full bg-gradient-to-r from-accent-500 to-cyan-accent
                                                transition-[width] duration-1000 ease-out"
                                         x-bind:style="shown ? 'width: {{ $skill->level }}%' : 'width: 0%'"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </x-section>

    {{-- ───────────────── Full stack list ───────────────── --}}
    <x-section eyebrow="Toolkit" title="Everything I work with" class="!pt-0">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($technologies as $category => $items)
                <div class="panel p-6">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-accent-300">
                        {{ \App\Models\Technology::CATEGORIES[$category] ?? Str::headline($category) }}
                    </h3>
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        @foreach ($items as $technology)
                            <span class="inline-flex items-center gap-2 rounded-md border border-white/8 bg-white/5
                                         px-2 py-1 font-mono text-[11px] text-muted-strong">
                                <span class="h-1.5 w-1.5 rounded-full"
                                      style="background-color: {{ $technology->color ?? '#4f9dff' }}"></span>
                                {{ $technology->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-section>

    {{-- ───────────────── Experience ───────────────── --}}
    <x-section eyebrow="Career" title="Experience" class="!pt-0">
        <ol>
            @foreach ($experiences as $experience)
                <x-timeline-entry :experience="$experience" detailed />
            @endforeach
        </ol>

        <div class="panel flex flex-col items-center gap-4 p-8 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="text-lg font-semibold text-white">Want this on one page?</h2>
                <p class="mt-1 text-sm text-muted">The résumé version, ready for print or your ATS.</p>
            </div>
            <div class="flex shrink-0 gap-3">
                <a href="{{ route('resume.show') }}" class="btn-ghost !py-2.5 !text-sm">View résumé</a>
                <a href="{{ route('resume.download') }}" class="btn-primary !py-2.5 !text-sm">
                    <x-icon name="download" class="h-4 w-4" />
                    PDF
                </a>
            </div>
        </div>
    </x-section>
</x-layout>
