@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">
        <div class="flex flex-1 items-center justify-between gap-3">
            @if ($paginator->onFirstPage())
                <span class="btn-ghost cursor-not-allowed !py-2.5 !text-sm opacity-40">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-ghost !py-2.5 !text-sm">
                    <x-icon name="arrow-left" class="h-4 w-4" />
                    Previous
                </a>
            @endif

            <span class="font-mono text-xs text-muted">
                Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-ghost !py-2.5 !text-sm">
                    Next
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            @else
                <span class="btn-ghost cursor-not-allowed !py-2.5 !text-sm opacity-40">
                    Next
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
