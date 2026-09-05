<x-admin-layout title="Services" heading="Services" description="The “What I build” cards on the home page.">
    <x-slot:actions>
        <a href="{{ route('admin.services.create') }}" class="btn-primary !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            New
        </a>
    </x-slot:actions>

    <div class="grid gap-4 sm:grid-cols-2">
        @forelse ($services as $service)
            <div class="panel p-6">
                <div class="flex items-start justify-between gap-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                                 border border-accent-400/25 bg-accent-500/10 text-accent-300">
                        <x-icon :name="$service->icon" class="h-4 w-4" />
                    </span>

                    <div class="flex shrink-0 gap-2">
                        <a href="{{ route('admin.services.edit', $service) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                  text-xs font-medium text-muted transition hover:text-white">
                            <x-icon name="edit" class="h-3.5 w-3.5" />
                            Edit
                        </a>
                        <x-admin.delete-button
                            :action="route('admin.services.destroy', $service)"
                            :confirm="'Delete “'.$service->title.'”?'" />
                    </div>
                </div>

                <h2 class="mt-4 text-base font-semibold text-white">{{ $service->title }}</h2>
                @if ($service->subtitle)
                    <p class="mt-0.5 font-mono text-xs text-accent-300">{{ $service->subtitle }}</p>
                @endif
                <p class="mt-3 text-sm leading-relaxed text-muted">{{ Str::limit($service->description, 160) }}</p>

                <div class="mt-4 flex items-center gap-2 border-t border-white/8 pt-4 text-[11px] text-muted">
                    <span class="font-mono">order {{ $service->sort_order }}</span>
                    <span class="text-white/20">·</span>
                    <span>{{ count($service->bullets ?? []) }} bullets</span>
                    <span class="text-white/20">·</span>
                    <span @class([
                        'font-medium',
                        'text-emerald-300' => $service->is_published,
                        'text-muted' => ! $service->is_published,
                    ])>{{ $service->is_published ? 'Live' : 'Hidden' }}</span>
                </div>
            </div>
        @empty
            <div class="panel p-12 text-center text-sm text-muted sm:col-span-2">
                No services yet.
                <a href="{{ route('admin.services.create') }}" class="text-accent-300 hover:underline">Add the first</a>.
            </div>
        @endforelse
    </div>
</x-admin-layout>
