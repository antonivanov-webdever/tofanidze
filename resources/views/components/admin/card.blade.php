@props(['title' => null, 'description' => null])

<section {{ $attributes->merge(['class' => 'panel p-6 sm:p-7']) }}>
    @if ($title)
        <header class="mb-6 border-b border-white/8 pb-4">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-accent-300">{{ $title }}</h2>
            @if ($description)
                <p class="mt-1.5 text-xs text-muted">{{ $description }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
