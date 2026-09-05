@php
    $groupTitles = [
        'profile' => ['Profile', 'Your name, headline and the copy shown across the site.'],
        'contact' => ['Contact & availability', 'How people reach you, and where form submissions land.'],
        'social' => ['Social links', 'Leave a field empty to hide that link.'],
        'seo' => ['SEO defaults', 'Used on the home page and as fallbacks everywhere else.'],
    ];
@endphp

<x-admin-layout title="Settings" heading="Site settings" description="These values override the defaults in config/site.php.">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        @foreach ($groupTitles as $group => [$title, $description])
            @php $groupFields = $fields[$group] ?? collect(); @endphp
            @continue($groupFields->isEmpty())

            <x-admin.card :title="$title" :description="$description">
                <div class="space-y-5">
                    @foreach ($groupFields as $field)
                        @if ($field['type'] === 'boolean')
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-white/10
                                          bg-white/[0.02] p-4 transition hover:border-white/20">
                                <input type="hidden" name="settings[{{ $field['key'] }}]" value="0">
                                <input type="checkbox" name="settings[{{ $field['key'] }}]" value="1"
                                       @checked(old("settings.{$field['key']}", $field['value']))
                                       class="mt-0.5 h-4 w-4 shrink-0 rounded border-white/20 bg-ink-900
                                              text-accent-500 focus:ring-accent-500/40">
                                <span>
                                    <span class="block text-sm font-medium text-white">{{ $field['label'] }}</span>
                                    @if ($field['hint'])
                                        <span class="mt-0.5 block text-xs text-muted">{{ $field['hint'] }}</span>
                                    @endif
                                </span>
                            </label>
                        @elseif ($field['type'] === 'textarea')
                            <div>
                                <label for="setting-{{ $field['key'] }}" class="field-label">{{ $field['label'] }}</label>
                                <textarea id="setting-{{ $field['key'] }}"
                                          name="settings[{{ $field['key'] }}]"
                                          rows="4"
                                          @class(['field resize-y', 'border-red-400/50' => $errors->has("settings.{$field['key']}")])>{{ old("settings.{$field['key']}", $field['value']) }}</textarea>
                                @error("settings.{$field['key']}")
                                    <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                                @else
                                    @if ($field['hint'])
                                        <p class="mt-1.5 text-xs text-muted">{{ $field['hint'] }}</p>
                                    @endif
                                @enderror
                            </div>
                        @else
                            <div>
                                <label for="setting-{{ $field['key'] }}" class="field-label">{{ $field['label'] }}</label>
                                <input type="{{ $field['type'] }}"
                                       id="setting-{{ $field['key'] }}"
                                       name="settings[{{ $field['key'] }}]"
                                       value="{{ old("settings.{$field['key']}", $field['value']) }}"
                                       @class(['field', 'border-red-400/50' => $errors->has("settings.{$field['key']}")])>
                                @error("settings.{$field['key']}")
                                    <p class="mt-1.5 text-xs text-red-300">{{ $message }}</p>
                                @else
                                    @if ($field['hint'])
                                        <p class="mt-1.5 text-xs text-muted">{{ $field['hint'] }}</p>
                                    @endif
                                @enderror
                            </div>
                        @endif
                    @endforeach
                </div>
            </x-admin.card>
        @endforeach

        <div class="sticky bottom-6 flex items-center justify-between gap-4 rounded-2xl border border-white/10
                    bg-ink-900/95 p-4 backdrop-blur-xl">
            <p class="text-xs text-muted">Changes apply immediately across the site.</p>
            <button type="submit" class="btn-primary !py-2.5 !text-sm">Save settings</button>
        </div>
    </form>
</x-admin-layout>
