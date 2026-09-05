@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'id' => null,
    'align' => 'left',
])

<section @if ($id) id="{{ $id }}" @endif
         {{ $attributes->merge(['class' => 'mx-auto max-w-6xl px-5 py-20 sm:px-8 sm:py-24']) }}>
    @if ($eyebrow || $title || $lead)
        <div @class([
            'max-w-2xl',
            'mx-auto text-center' => $align === 'center',
        ])>
            @if ($eyebrow)
                <p class="eyebrow">
                    <span class="h-px w-6 bg-accent-400/60"></span>
                    {{ $eyebrow }}
                </p>
            @endif

            @if ($title)
                <h2 class="mt-4 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                    {{ $title }}
                </h2>
            @endif

            @if ($lead)
                <p class="mt-4 text-base leading-relaxed text-muted">
                    {{ $lead }}
                </p>
            @endif
        </div>
    @endif

    <div @class(['mt-12' => $eyebrow || $title || $lead])>
        {{ $slot }}
    </div>
</section>
