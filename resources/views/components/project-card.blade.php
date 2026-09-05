@props(['project', 'featured' => false])

<article x-data="reveal()"
         x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
         class="panel panel-hover group relative flex flex-col overflow-hidden transition-all duration-700">
    <a href="{{ route('projects.show', $project) }}" class="absolute inset-0 z-10" aria-label="{{ $project->title }}">
        <span class="sr-only">Read the {{ $project->title }} case study</span>
    </a>

    @if ($project->coverUrl())
        <div class="aspect-[16/9] overflow-hidden border-b border-white/8">
            <img src="{{ $project->coverUrl() }}" alt=""
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]"
                 loading="lazy">
        </div>
    @endif

    <div class="flex flex-1 flex-col p-6 sm:p-7">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-muted">
            @if ($project->industry)
                <span class="font-medium text-accent-300">{{ $project->industry }}</span>
                <span class="text-white/20">/</span>
            @endif
            @if ($project->year)
                <span class="font-mono">{{ $project->year }}</span>
            @endif
            @if ($project->is_featured && $featured)
                <span class="ml-auto chip !py-0.5 !text-[10px] uppercase tracking-wider">
                    <x-icon name="zap" class="h-3 w-3 text-accent-300" />
                    Featured
                </span>
            @endif
        </div>

        <h3 class="mt-3 text-xl font-semibold leading-snug tracking-tight text-white transition group-hover:text-accent-300">
            {{ $project->title }}
        </h3>

        <p class="mt-3 flex-1 text-sm leading-relaxed text-muted">
            {{ Str::limit($project->summary, $featured ? 210 : 150) }}
        </p>

        @if ($featured && $project->metrics)
            <dl class="mt-6 grid grid-cols-3 gap-3 border-t border-white/8 pt-5">
                @foreach (array_slice($project->metrics, 0, 3) as $metric)
                    <div>
                        <dt class="sr-only">{{ $metric['label'] }}</dt>
                        <dd>
                            <span class="block font-mono text-base font-semibold text-white">{{ $metric['value'] }}</span>
                            <span class="mt-1 block text-[11px] leading-tight text-muted">{{ $metric['label'] }}</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        @endif

        <div class="mt-6 flex flex-wrap items-center gap-1.5">
            @foreach ($project->technologies->take(5) as $technology)
                <span class="rounded-md border border-white/8 bg-white/5 px-2 py-1 font-mono text-[11px] text-muted-strong">
                    {{ $technology->name }}
                </span>
            @endforeach
            @if ($project->technologies->count() > 5)
                <span class="px-1 font-mono text-[11px] text-muted">+{{ $project->technologies->count() - 5 }}</span>
            @endif
        </div>

        <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-white transition group-hover:gap-3 group-hover:text-accent-300">
            Read case study
            <x-icon name="arrow-right" class="h-4 w-4" />
        </span>
    </div>
</article>
