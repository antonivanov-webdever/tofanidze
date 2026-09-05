<x-layout
    title="Résumé"
    description="Full-stack engineer résumé — PHP (Laravel, Yii), Node.js (NestJS), Vue and React, with a focus on B2B marketing platforms and CRM integrations."
>
    <x-page-header
        eyebrow="Résumé"
        :title="$site->name()"
        :lead="$site->get('summary')"
    >
        <x-slot:meta>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-muted">
                <span class="font-mono text-accent-300">{{ $site->get('role') }}</span>
                <a href="mailto:{{ $site->get('email') }}" class="inline-flex items-center gap-2 hover:text-white">
                    <x-icon name="mail" class="h-4 w-4" />
                    {{ $site->get('email') }}
                </a>
                @if ($site->get('location'))
                    <span class="inline-flex items-center gap-2">
                        <x-icon name="map-pin" class="h-4 w-4" />
                        {{ $site->get('location') }}
                    </span>
                @endif
            </div>

            <div class="mt-7 flex flex-wrap gap-3">
                <a href="{{ route('resume.download') }}" class="btn-primary !py-2.5 !text-sm">
                    <x-icon name="download" class="h-4 w-4" />
                    Download PDF
                </a>
                <a href="{{ route('contact.show') }}" class="btn-ghost !py-2.5 !text-sm">Get in touch</a>
            </div>
        </x-slot:meta>
    </x-page-header>

    <div class="mx-auto max-w-4xl px-5 py-16 sm:px-8">
        {{-- Experience --}}
        <section>
            <h2 class="flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-accent-300">
                <x-icon name="briefcase" class="h-4 w-4" />
                Experience
            </h2>

            <ol class="mt-8">
                @foreach ($experiences as $experience)
                    <x-timeline-entry :experience="$experience" detailed />
                @endforeach
            </ol>
        </section>

        {{-- Skills --}}
        <section class="mt-6">
            <h2 class="flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-accent-300">
                <x-icon name="code" class="h-4 w-4" />
                Technical skills
            </h2>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                @foreach ($skillCategories as $category)
                    <div class="panel p-6">
                        <h3 class="text-sm font-semibold text-white">{{ $category->name }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-muted-strong">
                            {{ $category->skills->pluck('name')->implode(' · ') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Selected projects --}}
        <section class="mt-14">
            <h2 class="flex items-center gap-3 text-sm font-semibold uppercase tracking-wider text-accent-300">
                <x-icon name="grid" class="h-4 w-4" />
                Selected projects
            </h2>

            <div class="mt-8 space-y-4">
                @foreach ($projects as $project)
                    <a href="{{ route('projects.show', $project) }}" class="panel panel-hover group block p-6">
                        <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                            <h3 class="text-base font-semibold text-white transition group-hover:text-accent-300">
                                {{ $project->title }}
                            </h3>
                            <span class="font-mono text-xs text-muted">{{ $project->year }}</span>
                        </div>
                        <p class="mt-2 text-sm leading-relaxed text-muted">{{ $project->summary }}</p>
                        <p class="mt-3 font-mono text-[11px] text-muted-strong">
                            {{ $project->technologies->pluck('name')->implode(' · ') }}
                        </p>
                    </a>
                @endforeach
            </div>
        </section>

        <div class="panel mt-14 flex flex-col items-center gap-4 p-8 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="text-lg font-semibold text-white">Need this as a file?</h2>
                <p class="mt-1 text-sm text-muted">One-page PDF, generated from this page.</p>
            </div>
            <a href="{{ route('resume.download') }}" class="btn-primary shrink-0 !py-2.5 !text-sm">
                <x-icon name="download" class="h-4 w-4" />
                Download PDF
            </a>
        </div>
    </div>
</x-layout>
