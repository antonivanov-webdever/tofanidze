<x-admin-layout title="Technologies" heading="Technologies" description="The stack tags attached to case studies and shown in the toolkit sections.">
    <x-slot:actions>
        <a href="{{ route('admin.technologies.create') }}" class="btn-primary !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            New
        </a>
    </x-slot:actions>

    <div class="space-y-6">
        @foreach (\App\Models\Technology::CATEGORIES as $key => $label)
            @php $items = $technologies[$key] ?? collect(); @endphp

            <x-admin.card :title="$label" :description="$items->count().' '.Str::plural('technology', $items->count())">
                @if ($items->isEmpty())
                    <p class="py-4 text-sm text-muted">Nothing in this group yet.</p>
                @else
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($items as $technology)
                            <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.02] p-3">
                                <span class="h-3 w-3 shrink-0 rounded-full"
                                      style="background-color: {{ $technology->color ?? '#4f9dff' }}"></span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-white">
                                        {{ $technology->name }}
                                        @if ($technology->is_featured)
                                            <x-icon name="zap" class="ml-1 inline h-3 w-3 text-accent-300" />
                                        @endif
                                    </p>
                                    <p class="font-mono text-[11px] text-muted">
                                        {{ $technology->projects_count }} {{ Str::plural('project', $technology->projects_count) }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 gap-1">
                                    <a href="{{ route('admin.technologies.edit', $technology) }}"
                                       class="rounded-lg border border-white/10 p-1.5 text-muted transition hover:text-white"
                                       aria-label="Edit {{ $technology->name }}">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                    </a>
                                    <x-admin.delete-button
                                        :action="route('admin.technologies.destroy', $technology)"
                                        label=""
                                        :confirm="'Delete '.$technology->name.'? It will be removed from '.$technology->projects_count.' project(s).'"
                                        class="!px-1.5 !py-1.5" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.card>
        @endforeach
    </div>
</x-admin-layout>
