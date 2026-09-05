@props(['eyebrow' => null, 'title', 'lead' => null, 'meta' => null])

<section class="relative overflow-hidden border-b border-white/8">
    <div class="pointer-events-none absolute inset-0 bg-grid opacity-70" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -top-32 left-1/3 h-72 w-[560px] glow-accent opacity-35" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-6xl px-5 py-16 sm:px-8 sm:py-20">
        @if ($eyebrow)
            <p class="eyebrow">
                <span class="h-px w-6 bg-accent-400/60"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h1 class="mt-4 max-w-3xl text-4xl font-bold tracking-tight text-white sm:text-5xl">
            {{ $title }}
        </h1>

        @if ($lead)
            <p class="mt-5 max-w-2xl text-base leading-relaxed text-muted sm:text-[17px]">
                {{ $lead }}
            </p>
        @endif

        @if ($meta)
            <div class="mt-8">{{ $meta }}</div>
        @endif

        {{ $slot ?? '' }}
    </div>
</section>
