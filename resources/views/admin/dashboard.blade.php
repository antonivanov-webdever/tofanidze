<x-admin-layout title="Overview" heading="Overview" description="Everything on the site, in one place.">
    <x-slot:actions>
        <a href="{{ route('admin.projects.create') }}" class="btn-primary !px-4 !py-2 !text-xs">
            <x-icon name="plus" class="h-3.5 w-3.5" />
            New case study
        </a>
    </x-slot:actions>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($stats as $stat)
            <a href="{{ route($stat['route']) }}" class="panel panel-hover group p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-muted">{{ $stat['label'] }}</p>
                        <p class="mt-3 font-mono text-3xl font-semibold text-white">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs text-muted">{{ $stat['sub'] }}</p>
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-accent-400/25
                                 bg-accent-500/10 text-accent-300">
                        <x-icon :name="$stat['icon']" class="h-4 w-4" />
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-admin.card title="Recent messages">
            @forelse ($recentMessages as $message)
                <a href="{{ route('admin.messages.show', $message) }}"
                   class="flex items-start gap-3 border-b border-white/8 py-3 first:pt-0 last:border-0 last:pb-0
                          transition hover:opacity-80">
                    @if ($message->isUnread())
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent-400" title="Unread"></span>
                    @else
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-white/15"></span>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex items-baseline justify-between gap-3">
                            <p class="truncate text-sm font-medium text-white">{{ $message->name }}</p>
                            <span class="shrink-0 font-mono text-[11px] text-muted">
                                {{ $message->created_at->diffForHumans(short: true) }}
                            </span>
                        </div>
                        <p class="mt-0.5 truncate text-xs text-muted">
                            {{ $message->subject ?: Str::limit($message->message, 60) }}
                        </p>
                    </div>
                </a>
            @empty
                <p class="py-6 text-center text-sm text-muted">No messages yet.</p>
            @endforelse

            @if ($recentMessages->isNotEmpty())
                <a href="{{ route('admin.messages.index') }}"
                   class="mt-5 inline-flex items-center gap-2 text-xs font-semibold text-accent-300 hover:underline">
                    All messages
                    <x-icon name="arrow-right" class="h-3.5 w-3.5" />
                </a>
            @endif
        </x-admin.card>

        <x-admin.card title="Case studies">
            @forelse ($recentProjects as $project)
                <a href="{{ route('admin.projects.edit', $project) }}"
                   class="flex items-center gap-3 border-b border-white/8 py-3 first:pt-0 last:border-0 last:pb-0
                          transition hover:opacity-80">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-white">{{ $project->title }}</p>
                        <p class="mt-0.5 text-xs text-muted">
                            {{ $project->year }}@if ($project->industry) · {{ $project->industry }}@endif
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1.5">
                        @if ($project->is_featured)
                            <span class="rounded border border-accent-400/25 bg-accent-500/10 px-1.5 py-0.5
                                         text-[10px] font-medium text-accent-300">Featured</span>
                        @endif
                        <span @class([
                            'rounded px-1.5 py-0.5 text-[10px] font-medium',
                            'border border-emerald-400/25 bg-emerald-400/10 text-emerald-300' => $project->is_published,
                            'border border-white/10 bg-white/5 text-muted' => ! $project->is_published,
                        ])>
                            {{ $project->is_published ? 'Live' : 'Draft' }}
                        </span>
                    </div>
                </a>
            @empty
                <p class="py-6 text-center text-sm text-muted">No case studies yet.</p>
            @endforelse

            <a href="{{ route('admin.projects.index') }}"
               class="mt-5 inline-flex items-center gap-2 text-xs font-semibold text-accent-300 hover:underline">
                Manage case studies
                <x-icon name="arrow-right" class="h-3.5 w-3.5" />
            </a>
        </x-admin.card>
    </div>

    <x-admin.card title="Quick actions" class="mt-6">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['New case study', 'admin.projects.create', 'briefcase'],
                ['New article', 'admin.posts.create', 'file-text'],
                ['Add a role', 'admin.experiences.create', 'calendar'],
                ['Site settings', 'admin.settings.edit', 'settings'],
            ] as [$label, $route, $icon])
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.02] p-4
                          text-sm font-medium text-muted-strong transition hover:border-accent-400/40 hover:text-white">
                    <x-icon :name="$icon" class="h-4 w-4 text-accent-300" />
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </x-admin.card>
</x-admin-layout>
