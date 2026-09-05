<x-admin-layout
    title="Message"
    :heading="$message->subject ?: 'Message from '.$message->name"
    :description="$message->created_at->format('d F Y, H:i')"
>
    <x-slot:actions>
        <a href="{{ route('admin.messages.index') }}" class="btn-ghost !px-4 !py-2 !text-xs">Back</a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        <x-admin.card title="Message">
            <div class="whitespace-pre-wrap text-[15px] leading-relaxed text-muted-strong">{{ $message->message }}</div>

            <div class="mt-8 flex flex-wrap gap-3 border-t border-white/8 pt-6">
                <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'your enquiry')) }}"
                   class="btn-primary !py-2.5 !text-sm">
                    <x-icon name="mail" class="h-4 w-4" />
                    Reply by email
                </a>

                <form method="POST" action="{{ route('admin.messages.toggle-read', $message) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-ghost !py-2.5 !text-sm">
                        Mark as {{ $message->isUnread() ? 'read' : 'unread' }}
                    </button>
                </form>
            </div>
        </x-admin.card>

        <div class="space-y-6">
            <x-admin.card title="Sender">
                <dl class="space-y-4">
                    @foreach (array_filter([
                        'Name' => $message->name,
                        'Email' => $message->email,
                        'Company' => $message->company,
                        'Budget' => $message->budget,
                        'Subject' => $message->subject,
                    ]) as $label => $value)
                        <div class="border-b border-white/8 pb-3 last:border-0 last:pb-0">
                            <dt class="text-xs text-muted">{{ $label }}</dt>
                            <dd class="mt-1 break-words text-sm font-medium text-white">
                                @if ($label === 'Email')
                                    <a href="mailto:{{ $value }}" class="hover:text-accent-300">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </x-admin.card>

            <x-admin.card title="Technical">
                <dl class="space-y-3 font-mono text-[11px] text-muted">
                    <div>
                        <dt class="text-muted">Received</dt>
                        <dd class="mt-0.5 text-muted-strong">{{ $message->created_at->format('d M Y, H:i:s') }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted">IP address</dt>
                        <dd class="mt-0.5 text-muted-strong">{{ $message->ip_address ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted">User agent</dt>
                        <dd class="mt-0.5 break-words text-muted-strong">{{ $message->user_agent ?: '—' }}</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-white/8 pt-5">
                    <x-admin.delete-button
                        :action="route('admin.messages.destroy', $message)"
                        label="Delete message"
                        confirm="Delete this message permanently?"
                        class="!px-4 !py-2.5" />
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>
