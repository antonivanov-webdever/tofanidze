<x-admin-layout title="Skills" heading="Skills" description="Grouped skills with proficiency levels, shown on the about page and in the résumé.">
    <x-slot:actions>
        <a href="{{ route('admin.skills.create') }}" class="btn-ghost !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            Skill
        </a>
        <a href="{{ route('admin.skill-categories.create') }}" class="btn-primary !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            Group
        </a>
    </x-slot:actions>

    <div class="space-y-4">
        @forelse ($categories as $category)
            <x-admin.card>
                <div class="flex flex-wrap items-start justify-between gap-4 border-b border-white/8 pb-4">
                    <div class="flex items-start gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                                     border border-accent-400/25 bg-accent-500/10 text-accent-300">
                            <x-icon :name="$category->icon ?: 'code'" class="h-4 w-4" />
                        </span>
                        <div>
                            <h2 class="text-base font-semibold text-white">{{ $category->name }}</h2>
                            @if ($category->description)
                                <p class="mt-0.5 text-xs text-muted">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex shrink-0 gap-2">
                        <a href="{{ route('admin.skills.create', ['category' => $category->id]) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                  text-xs font-medium text-muted transition hover:text-white">
                            <x-icon name="plus" class="h-3.5 w-3.5" />
                            Skill
                        </a>
                        <a href="{{ route('admin.skill-categories.edit', $category) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                  text-xs font-medium text-muted transition hover:text-white">
                            <x-icon name="edit" class="h-3.5 w-3.5" />
                            Edit
                        </a>
                        <x-admin.delete-button
                            :action="route('admin.skill-categories.destroy', $category)"
                            :confirm="'Delete “'.$category->name.'” and its '.$category->skills->count().' skill(s)?'" />
                    </div>
                </div>

                @if ($category->skills->isEmpty())
                    <p class="py-4 text-sm text-muted">No skills in this group yet.</p>
                @else
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ($category->skills as $skill)
                            <div class="flex items-center gap-4 rounded-xl border border-white/10 bg-white/[0.02] p-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-baseline justify-between gap-3">
                                        <span class="truncate text-sm font-medium text-white">{{ $skill->name }}</span>
                                        <span class="shrink-0 font-mono text-[11px] text-muted">
                                            {{ $skill->level }}%@if ($skill->years) · {{ $skill->years }}y @endif
                                        </span>
                                    </div>
                                    <div class="mt-2 h-1 overflow-hidden rounded-full bg-white/8">
                                        <div class="h-full rounded-full bg-gradient-to-r from-accent-500 to-cyan-accent"
                                             style="width: {{ $skill->level }}%"></div>
                                    </div>
                                </div>

                                <div class="flex shrink-0 gap-1">
                                    <a href="{{ route('admin.skills.edit', $skill) }}"
                                       class="rounded-lg border border-white/10 p-1.5 text-muted transition hover:text-white"
                                       aria-label="Edit {{ $skill->name }}">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                    </a>
                                    <x-admin.delete-button
                                        :action="route('admin.skills.destroy', $skill)"
                                        label=""
                                        :confirm="'Delete “'.$skill->name.'”?'"
                                        class="!px-1.5 !py-1.5" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.card>
        @empty
            <div class="panel p-12 text-center text-sm text-muted">
                No skill groups yet.
                <a href="{{ route('admin.skill-categories.create') }}" class="text-accent-300 hover:underline">
                    Create the first
                </a>.
            </div>
        @endforelse
    </div>
</x-admin-layout>
