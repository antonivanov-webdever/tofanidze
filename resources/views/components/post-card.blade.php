@props(['post'])

<article x-data="reveal()"
         x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
         class="panel panel-hover group relative flex flex-col p-6 transition-all duration-700 sm:p-7">
    <a href="{{ route('blog.show', $post) }}" class="absolute inset-0 z-10">
        <span class="sr-only">Read {{ $post->title }}</span>
    </a>

    <div class="flex items-center gap-3 text-xs text-muted">
        <time datetime="{{ $post->published_at?->toDateString() }}" class="font-mono">
            {{ $post->published_at?->format('d M Y') }}
        </time>
        <span class="text-white/20">·</span>
        <span>{{ $post->readingTime() }} min read</span>
    </div>

    <h3 class="mt-3 text-lg font-semibold leading-snug tracking-tight text-white transition group-hover:text-accent-300">
        {{ $post->title }}
    </h3>

    <p class="mt-3 flex-1 text-sm leading-relaxed text-muted">
        {{ Str::limit($post->excerpt, 165) }}
    </p>

    @if ($post->tags)
        <div class="mt-5 flex flex-wrap gap-1.5">
            @foreach (array_slice($post->tags, 0, 3) as $tag)
                <span class="rounded-md border border-white/8 bg-white/5 px-2 py-1 font-mono text-[11px] text-muted-strong">
                    {{ $tag }}
                </span>
            @endforeach
        </div>
    @endif
</article>
