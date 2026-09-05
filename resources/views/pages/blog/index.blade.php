<x-layout
    title="Writing"
    description="Notes on integration architecture, dashboard performance and legacy modernisation — written from production work, not theory."
>
    <x-page-header
        eyebrow="Writing"
        title="Notes from the integration layer"
        lead="What I learned building portals, dashboards and CRM syncs — the decisions that held up in production and the ones that did not."
    />

    <div class="mx-auto max-w-6xl px-5 py-14 sm:px-8">
        @if ($tags->isNotEmpty())
            <div class="mb-10 flex flex-wrap items-center gap-2">
                <a href="{{ route('blog.index') }}"
                   @class([
                       'rounded-lg border px-3 py-1.5 text-xs font-medium transition',
                       'border-accent-400/50 bg-accent-500/15 text-white' => ! $tag,
                       'border-white/10 bg-white/[0.03] text-muted hover:text-white' => $tag,
                   ])>
                    All topics
                </a>

                @foreach ($tags as $availableTag)
                    <a href="{{ route('blog.index', ['tag' => $availableTag]) }}"
                       @class([
                           'rounded-lg border px-3 py-1.5 font-mono text-xs transition',
                           'border-accent-400/50 bg-accent-500/15 text-white' => $tag === $availableTag,
                           'border-white/10 bg-white/[0.03] text-muted hover:text-white' => $tag !== $availableTag,
                       ])>
                        {{ $availableTag }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($posts->isEmpty())
            <div class="panel p-12 text-center">
                <p class="text-sm text-muted">No articles here yet.</p>
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif

        <div class="panel mt-12 flex flex-col items-center gap-4 p-7 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="text-base font-semibold text-white">Prefer a reader?</h2>
                <p class="mt-1 text-sm text-muted">Everything here is available as an RSS feed.</p>
            </div>
            <a href="{{ route('feed') }}" class="btn-ghost !py-2.5 !text-sm shrink-0">
                Subscribe via RSS
                <x-icon name="arrow-up-right" class="h-4 w-4" />
            </a>
        </div>
    </div>
</x-layout>
