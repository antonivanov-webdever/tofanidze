<x-admin-layout
    title="Messages"
    heading="Messages"
    :description="$unreadCount > 0 ? $unreadCount.' unread' : 'Everything read.'"
>
    <x-slot:actions>
        <a href="{{ route('admin.messages.index') }}"
           @class([
               'rounded-lg border px-3 py-2 text-xs font-medium transition',
               'border-accent-400/50 bg-accent-500/15 text-white' => $filter !== 'unread',
               'border-white/10 text-muted hover:text-white' => $filter === 'unread',
           ])>
            All
        </a>
        <a href="{{ route('admin.messages.index', ['filter' => 'unread']) }}"
           @class([
               'rounded-lg border px-3 py-2 text-xs font-medium transition',
               'border-accent-400/50 bg-accent-500/15 text-white' => $filter === 'unread',
               'border-white/10 text-muted hover:text-white' => $filter !== 'unread',
           ])>
            Unread ({{ $unreadCount }})
        </a>
    </x-slot:actions>

    <div class="panel divide-y divide-white/8">
        @forelse ($messages as $message)
            <div class="flex items-start gap-4 p-5 transition hover:bg-white/[0.02]">
                <span @class([
                    'mt-2 h-2 w-2 shrink-0 rounded-full',
                    'bg-accent-400' => $message->isUnread(),
                    'bg-white/15' => ! $message->isUnread(),
                ])></span>

                <a href="{{ route('admin.messages.show', $message) }}" class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1">
                        <p @class([
                            'truncate text-sm',
                            'font-semibold text-white' => $message->isUnread(),
                            'font-medium text-muted-strong' => ! $message->isUnread(),
                        ])>
                            {{ $message->name }}
                            @if ($message->company)
                                <span class="text-muted">· {{ $message->company }}</span>
                            @endif
                        </p>
                        <span class="shrink-0 font-mono text-[11px] text-muted">
                            {{ $message->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>

                    <p class="mt-1 truncate text-sm text-white/80">
                        {{ $message->subject ?: 'No subject' }}
                    </p>
                    <p class="mt-1 truncate text-xs text-muted">{{ Str::limit($message->message, 120) }}</p>

                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-muted">
                        <span class="font-mono">{{ $message->email }}</span>
                        @if ($message->budget)
                            <span class="text-white/20">·</span>
                            <span>{{ $message->budget }}</span>
                        @endif
                    </div>
                </a>

                <div class="flex shrink-0 gap-2">
                    <form method="POST" action="{{ route('admin.messages.toggle-read', $message) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 px-2.5 py-1.5
                                       text-xs font-medium text-muted transition hover:text-white">
                            <x-icon name="{{ $message->isUnread() ? 'check' : 'eye' }}" class="h-3.5 w-3.5" />
                            {{ $message->isUnread() ? 'Read' : 'Unread' }}
                        </button>
                    </form>

                    <x-admin.delete-button
                        :action="route('admin.messages.destroy', $message)"
                        label=""
                        confirm="Delete this message permanently?"
                        class="!px-2 !py-1.5" />
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-sm text-muted">
                {{ $filter === 'unread' ? 'Nothing unread.' : 'No messages yet.' }}
            </div>
        @endforelse
    </div>

    @if ($messages->hasPages())
        <div class="mt-6">{{ $messages->links() }}</div>
    @endif
</x-admin-layout>
