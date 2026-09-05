<x-admin-layout title="Case studies" heading="Case studies" description="Portfolio entries shown on the public site.">
    <x-slot:actions>
        <a href="{{ route('admin.projects.create') }}" class="btn-primary !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            New
        </a>
    </x-slot:actions>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="border-b border-white/8 text-xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Title</th>
                        <th class="px-6 py-4 font-semibold">Stack</th>
                        <th class="px-6 py-4 font-semibold">Year</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr class="border-b border-white/5 transition last:border-0 hover:bg-white/[0.02]">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.projects.edit', $project) }}"
                                   class="font-medium text-white hover:text-accent-300">
                                    {{ $project->title }}
                                </a>
                                <p class="mt-0.5 font-mono text-[11px] text-muted">/{{ $project->slug }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-muted">
                                    {{ $project->technologies->take(3)->pluck('name')->implode(' · ') }}
                                    @if ($project->technologies->count() > 3)
                                        +{{ $project->technologies->count() - 3 }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-muted">{{ $project->year ?: '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    <span @class([
                                        'rounded px-2 py-0.5 text-[10px] font-medium',
                                        'border border-emerald-400/25 bg-emerald-400/10 text-emerald-300' => $project->is_published,
                                        'border border-white/10 bg-white/5 text-muted' => ! $project->is_published,
                                    ])>
                                        {{ $project->is_published ? 'Live' : 'Draft' }}
                                    </span>
                                    @if ($project->is_featured)
                                        <span class="rounded border border-accent-400/25 bg-accent-500/10 px-2 py-0.5
                                                     text-[10px] font-medium text-accent-300">Featured</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    @if ($project->is_published)
                                        <a href="{{ route('projects.show', $project) }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                                  text-xs font-medium text-muted transition hover:text-white">
                                            <x-icon name="eye" class="h-3.5 w-3.5" />
                                            View
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.projects.edit', $project) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                              text-xs font-medium text-muted transition hover:text-white">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                        Edit
                                    </a>
                                    <x-admin.delete-button
                                        :action="route('admin.projects.destroy', $project)"
                                        :confirm="'Delete “'.$project->title.'”? This cannot be undone.'" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-muted">
                                No case studies yet.
                                <a href="{{ route('admin.projects.create') }}" class="text-accent-300 hover:underline">
                                    Create the first one
                                </a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($projects->hasPages())
        <div class="mt-6">{{ $projects->links() }}</div>
    @endif
</x-admin-layout>
