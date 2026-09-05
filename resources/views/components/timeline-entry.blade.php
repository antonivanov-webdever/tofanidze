@props(['experience', 'detailed' => false])

<li x-data="reveal()"
    x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
    class="relative pl-8 transition-all duration-700 sm:pl-10">
    {{-- Rail --}}
    <span class="absolute left-0 top-2 h-full w-px bg-gradient-to-b from-white/15 to-transparent" aria-hidden="true"></span>
    <span @class([
        'absolute -left-[5px] top-2 h-[11px] w-[11px] rounded-full border-2',
        'border-accent-400 bg-accent-400 shadow-[0_0_16px_2px_rgba(79,157,255,0.5)]' => $experience->is_current,
        'border-white/25 bg-ink-850' => ! $experience->is_current,
    ])></span>

    <div class="pb-10">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
            <span class="font-mono text-xs text-accent-300">{{ $experience->period() }}</span>
            <span class="text-white/20">·</span>
            <span class="font-mono text-xs text-muted">{{ $experience->durationLabel() }}</span>
            @if ($experience->is_current)
                <span class="rounded-full border border-emerald-400/25 bg-emerald-400/10 px-2 py-0.5
                             text-[10px] font-semibold uppercase tracking-wider text-emerald-300">
                    Current
                </span>
            @endif
        </div>

        <h3 class="mt-2 text-lg font-semibold tracking-tight text-white">{{ $experience->position }}</h3>

        <p class="mt-1 text-sm text-muted-strong">
            @if ($experience->company_url)
                <a href="{{ $experience->company_url }}" target="_blank" rel="noopener noreferrer"
                   class="text-white hover:text-accent-300">{{ $experience->company }}</a>
            @else
                <span class="text-white">{{ $experience->company }}</span>
            @endif
            @if ($experience->location)
                <span class="text-muted"> · {{ $experience->location }}</span>
            @endif
            @if ($experience->employment_type)
                <span class="text-muted"> · {{ $experience->employment_type }}</span>
            @endif
        </p>

        @if ($experience->description)
            <p class="mt-4 max-w-2xl text-sm leading-relaxed text-muted">{{ $experience->description }}</p>
        @endif

        @if ($detailed && $experience->highlights)
            <ul class="mt-5 max-w-2xl space-y-2.5">
                @foreach ($experience->highlights as $highlight)
                    <li class="flex gap-3 text-sm leading-relaxed text-muted-strong">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-accent-400" />
                        <span>{{ $highlight }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($experience->stack)
            <div class="mt-5 flex flex-wrap gap-1.5">
                @foreach ($experience->stack as $item)
                    <span class="rounded-md border border-white/8 bg-white/5 px-2 py-1 font-mono text-[11px] text-muted-strong">
                        {{ $item }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</li>
