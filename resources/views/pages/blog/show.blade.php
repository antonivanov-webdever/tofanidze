<x-layout
    :title="$post->meta_title ?: $post->title"
    :description="$post->meta_description ?: $post->excerpt"
    :image="$post->coverUrl()"
    type="article"
    :published-at="$post->published_at?->toIso8601String()"
>
    @push('head')
        <script type="application/ld+json">
            {!! json_encode(array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->title,
                'description' => $post->excerpt,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified' => $post->updated_at?->toIso8601String(),
                'author' => ['@type' => 'Person', 'name' => $site->name(), 'url' => route('home')],
                'mainEntityOfPage' => route('blog.show', $post),
                'keywords' => implode(', ', $post->tags ?? []),
            ]), JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush

    <article>
        <header class="relative overflow-hidden border-b border-white/8">
            <div class="pointer-events-none absolute inset-0 bg-grid opacity-70" aria-hidden="true"></div>

            <div class="relative mx-auto max-w-3xl px-5 py-14 sm:px-8 sm:py-18">
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center gap-2 text-sm font-medium text-muted transition hover:text-white">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    All articles
                </a>

                <div class="mt-8 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-muted">
                    <time datetime="{{ $post->published_at?->toDateString() }}" class="font-mono">
                        {{ $post->published_at?->format('d F Y') }}
                    </time>
                    <span class="text-white/20">·</span>
                    <span>{{ $post->readingTime() }} min read</span>
                    @if ($post->tags)
                        <span class="text-white/20">·</span>
                        <span class="font-mono text-accent-300">{{ implode(' / ', $post->tags) }}</span>
                    @endif
                </div>

                <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-white sm:text-4xl">
                    {{ $post->title }}
                </h1>

                @if ($post->excerpt)
                    <p class="mt-5 text-lg leading-relaxed text-muted-strong">{{ $post->excerpt }}</p>
                @endif
            </div>
        </header>

        @if ($post->coverUrl())
            <div class="mx-auto max-w-4xl px-5 pt-12 sm:px-8">
                <img src="{{ $post->coverUrl() }}" alt="" class="w-full rounded-2xl border border-white/10">
            </div>
        @endif

        <div class="mx-auto max-w-3xl px-5 py-14 sm:px-8">
            <div class="prose-site">
                {!! $post->renderedBody() !!}
            </div>

            <div class="mt-14 flex flex-col gap-5 border-t border-white/8 pt-8 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full border border-white/12
                                 bg-gradient-to-br from-white/12 to-transparent text-sm font-bold text-white">
                        {{ $site->initials() }}
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-white">{{ $site->name() }}</p>
                        <p class="text-xs text-muted">{{ $site->get('headline') }} · {{ $site->get('tagline') }}</p>
                    </div>
                </div>

                <a href="{{ route('contact.show') }}" class="btn-ghost !py-2.5 !text-sm">
                    Get in touch
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <div class="mx-auto max-w-6xl px-5 pb-16 sm:px-8">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-muted">Keep reading</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach ($related as $relatedPost)
                    <x-post-card :post="$relatedPost" />
                @endforeach
            </div>
        </div>
    @endif
</x-layout>
