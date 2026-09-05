<x-admin-layout title="Experience" heading="Experience" description="Career timeline shown on the about page and in the résumé.">
    <x-slot:actions>
        <a href="{{ route('admin.experiences.create') }}" class="btn-primary !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            New
        </a>
    </x-slot:actions>

    <div class="space-y-4">
        @forelse ($experiences as $experience)
            <div class="panel p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span class="font-mono text-xs text-accent-300">{{ $experience->period() }}</span>
                            @if ($experience->is_current)
                                <span class="rounded-full border border-emerald-400/25 bg-emerald-400/10 px-2 py-0.5
                                             text-[10px] font-semibold uppercase tracking-wider text-emerald-300">
                                    Current
                                </span>
                            @endif
                            <span class="font-mono text-[11px] text-muted">order {{ $experience->sort_order }}</span>
                        </div>

                        <h2 class="mt-2 text-base font-semibold text-white">{{ $experience->position }}</h2>
                        <p class="mt-0.5 text-sm text-muted">
                            {{ $experience->company }}@if ($experience->location) · {{ $experience->location }}@endif
                        </p>

                        @if ($experience->highlights)
                            <p class="mt-2 text-xs text-muted">
                                {{ count($experience->highlights) }} {{ Str::plural('highlight', count($experience->highlights)) }}
                            </p>
                        @endif
                    </div>

                    <div class="flex shrink-0 gap-2">
                        <a href="{{ route('admin.experiences.edit', $experience) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                  text-xs font-medium text-muted transition hover:text-white">
                            <x-icon name="edit" class="h-3.5 w-3.5" />
                            Edit
                        </a>
                        <x-admin.delete-button
                            :action="route('admin.experiences.destroy', $experience)"
                            :confirm="'Delete the role at '.$experience->company.'?'" />
                    </div>
                </div>
            </div>
        @empty
            <div class="panel p-12 text-center text-sm text-muted">
                No roles yet.
                <a href="{{ route('admin.experiences.create') }}" class="text-accent-300 hover:underline">Add the first</a>.
            </div>
        @endforelse
    </div>
</x-admin-layout>
