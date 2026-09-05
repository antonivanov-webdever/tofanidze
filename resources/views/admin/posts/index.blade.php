<x-admin-layout title="Articles" heading="Articles" description="Blog posts, written in Markdown.">
    <x-slot:actions>
        <a href="{{ route('admin.posts.create') }}" class="btn-primary !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            New
        </a>
    </x-slot:actions>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[680px] text-left text-sm">
                <thead class="border-b border-white/8 text-xs uppercase tracking-wider text-muted">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Title</th>
                        <th class="px-6 py-4 font-semibold">Tags</th>
                        <th class="px-6 py-4 font-semibold">Published</th>
                        <th class="px-6 py-4 font-semibold">Views</th>
                        <th class="px-6 py-4 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr class="border-b border-white/5 transition last:border-0 hover:bg-white/[0.02]">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.posts.edit', $post) }}"
                                   class="font-medium text-white hover:text-accent-300">{{ $post->title }}</a>
                                <p class="mt-0.5 font-mono text-[11px] text-muted">/blog/{{ $post->slug }}</p>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-muted">
                                {{ implode(' · ', array_slice($post->tags ?? [], 0, 3)) ?: '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($post->is_published)
                                    <span class="font-mono text-xs text-emerald-300">
                                        {{ $post->published_at?->format('d M Y') ?: 'Live' }}
                                    </span>
                                @else
                                    <span class="rounded border border-white/10 bg-white/5 px-2 py-0.5 text-[10px] text-muted">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-muted">{{ $post->views }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    @if ($post->is_published)
                                        <a href="{{ route('blog.show', $post) }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                                  text-xs font-medium text-muted transition hover:text-white">
                                            <x-icon name="eye" class="h-3.5 w-3.5" />
                                            View
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.posts.edit', $post) }}"
                                       class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                              text-xs font-medium text-muted transition hover:text-white">
                                        <x-icon name="edit" class="h-3.5 w-3.5" />
                                        Edit
                                    </a>
                                    <x-admin.delete-button
                                        :action="route('admin.posts.destroy', $post)"
                                        :confirm="'Delete “'.$post->title.'”? This cannot be undone.'" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-muted">
                                No articles yet.
                                <a href="{{ route('admin.posts.create') }}" class="text-accent-300 hover:underline">
                                    Write the first one
                                </a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($posts->hasPages())
        <div class="mt-6">{{ $posts->links() }}</div>
    @endif
</x-admin-layout>
